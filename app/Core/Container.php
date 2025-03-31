<?php

namespace App\Core;

class Container {
    private array $instances = [];

    public function has(string $key): bool
    {
        return isset($this->instances[$key]) || class_exists($key);
    }
    public function get(string $key) {
        return $this->instances[$key];
    }
    public function bind(string $key, callable $resolver) {
        $this->instances[$key] = $resolver;
    }

    public function make(string $class) {
        if (isset($this->instances[$class])) {
            return $this->instances[$class]($this);
        }

        if (!class_exists($class)) {
            throw new \Exception("Classe $class non trouvée.");
        }

        // 🔍 Récupérer le constructeur et ses dépendances
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            return new $class();
        }

        $params = $constructor->getParameters();
        $dependencies = [];

        foreach ($params as $param) {
            $type = $param->getType();
            if ($type && !$type->isBuiltin()) {
                $dependencies[] = $this->make($type->getName());
            } else {
                throw new \Exception("Impossible de résoudre la dépendance de $class.");
            }
        }

        return $reflection->newInstanceArgs($dependencies);
    }
}
