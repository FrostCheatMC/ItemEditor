<?php


namespace CortexPE\std;


final class ReflectionUtils {
	private static $propCache = [];
	private static $methCache = [];

	/**
	 * @param string $className
	 * @param object $instance
	 * @param string $propertyName
	 * @return mixed
	 * @throws \ReflectionException
	 */
	public static function getProperty(string $className, object $instance, string $propertyName): mixed {
		if(!isset(self::$propCache[$k = "$className - $propertyName"])) {
			$refClass = new \ReflectionClass($className);
			$refProp = $refClass->getProperty($propertyName);
			$refProp->setAccessible(true);
		} else {
			$refProp = self::$propCache[$k];
		}
		return $refProp->getValue($instance);
	}

	/**
	 * @param string $className
	 * @param object $instance
	 * @param string $propertyName
	 * @param $value
	 * @throws \ReflectionException
	 */
	public static function setProperty(string $className, object $instance, string $propertyName, $value): void {
		if(!isset(self::$propCache[$k = "$className - $propertyName"])) {
			$refClass = new \ReflectionClass($className);
			$refProp = $refClass->getProperty($propertyName);
			$refProp->setAccessible(true);
		} else {
			$refProp = self::$propCache[$k];
		}
		$refProp->setValue($instance, $value);
	}

	/**
	 * @param string $className
	 * @param object $instance
	 * @param string $methodName
	 * @param mixed ...$args
	 * @return mixed
	 * @throws \ReflectionException
	 */
	public static function invoke(string $className, object $instance, string $methodName, ...$args): mixed {
		if(!isset(self::$methCache[$k = "$className - $methodName"])) {
			$refClass = new \ReflectionClass($className);
			$refMeth = $refClass->getMethod($methodName);
			$refMeth->setAccessible(true);
		} else {
			$refMeth = self::$methCache[$k];
		}
		return $refMeth->invoke($instance, ...$args);
	}

	/**
	 * @param string $className
	 * @param string $methodName
	 * @param mixed ...$args
	 * @return mixed
	 * @throws \ReflectionException
	 */
	public static function invokeStatic(string $className, string $methodName, ...$args): mixed {
		if(!isset(self::$methCache[$k = "$className - $methodName"])) {
			$refClass = new \ReflectionClass($className);
			$refMeth = $refClass->getMethod($methodName);
			$refMeth->setAccessible(true);
		} else {
			$refMeth = self::$methCache[$k];
		}
		return $refMeth->invoke(null, ...$args);
	}

	/**
	 * @param string|object $class
	 * @param bool $autoload
	 * @return array
	 */
	public static function class_uses_deep(string|object $class, bool $autoload = true): array {
		$traits = [];
		do {
			$traits = array_merge(class_uses($class, $autoload), $traits);
		} while($class = get_parent_class($class));
		foreach($traits as $trait => $same) {
			$traits = array_merge(class_uses($trait, $autoload), $traits);
		}
		return array_unique($traits);
	}

	/**
	 * @param string|object $class
	 * @param string $traitName
	 * @param bool $autoload
	 * @return bool
	 */
	public static function hasTrait(string|object $class, string $traitName, bool $autoload = true): bool {
		return in_array($traitName, self::class_uses_deep($class, $autoload));
	}
}