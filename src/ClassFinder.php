<?php

namespace Gregoriohc\Seedable;

class ClassFinder
{
    public static function getClassesInNamespace($namespace, $basePath = null)
    {
        $basePath = ($basePath ?: base_path()) . '/';

        $files = scandir(self::getNamespaceDirectory($namespace, $basePath));

        $classes = array_map(function ($file) use ($namespace) {
            return $namespace . '\\' . str_replace('.php', '', $file);
        }, $files);

        return array_filter($classes, function ($possibleClass) {
            return class_exists($possibleClass);
        });
    }

    private static function getDefinedNamespaces($basePath)
    {
        $composerJsonPath = $basePath . 'composer.json';
        $composerConfig = json_decode(file_get_contents($composerJsonPath));

        //Apparently PHP doesn't like hyphens, so we use variable variables instead.
        $psr4 = "psr-4";
        return (array)$composerConfig->autoload->$psr4;
    }

    private static function getNamespaceDirectory($namespace, $basePath)
    {
        $composerNamespaces = self::getDefinedNamespaces($basePath);

        $namespaceFragments = explode('\\', $namespace);
        $undefinedNamespaceFragments = [];

        while ($namespaceFragments) {
            $possibleNamespace = implode('\\', $namespaceFragments) . '\\';

            if (array_key_exists($possibleNamespace, $composerNamespaces)) {
                return realpath($basePath . $composerNamespaces[$possibleNamespace] . implode('/', $undefinedNamespaceFragments));
            }

            $undefinedNamespaceFragments[] = array_pop($namespaceFragments);
        }

        return false;
    }
}