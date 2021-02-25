<?php

namespace BenTools\WebpackEncoreResolver;

final class AssetPathResolver
{
    /**
     * @var string
     */
    private $directory;

    /**
     * @var array
     */
    private $entrypoints;

    /**
     * @var array
     */
    private static $filesMap = [];

    /**
     * @var array
     */
    private $manifest;

    /**
     * @var string
     */
    private static $DEFAULT_DIRECTORY;

    public function __construct($directory = null)
    {
        $this->directory =
            null !== $directory ? $directory : $this->getDefaultDirectory();
    }

    public static function setDefaultDirectory($directory)
    {
        self::$DEFAULT_DIRECTORY = $directory;
    }

    /**
     * @return string
     */
    private function getDefaultDirectory()
    {
        if (isset(self::$DEFAULT_DIRECTORY)) {
            return self::$DEFAULT_DIRECTORY;
        }

        $docRoot = isset($_SERVER['DOCUMENT_ROOT'])
            ? $_SERVER['DOCUMENT_ROOT']
            : \getcwd();

        return \rtrim($docRoot, \DIRECTORY_SEPARATOR) .
            \DIRECTORY_SEPARATOR .
            'build';
    }

    /**
     * @return array
     */
    private function getEntrypoints()
    {
        if (null === $this->entrypoints) {
            $file =
                $this->directory . \DIRECTORY_SEPARATOR . 'entrypoints.json';
            $content = \file_get_contents($file);
            if (false === $content) {
                throw new \RuntimeException(
                    \sprintf('Unable to read file "%s"', $file)
                );
            }

            $json = \json_decode($content, true);
            if (\JSON_ERROR_NONE !== \json_last_error()) {
                throw new \RuntimeException(
                    \sprintf('Unable to decode json file "%s"', $file)
                );
            }

            $this->entrypoints = $json['entrypoints'];
        }
        return $this->entrypoints;
    }

    /**
     * @return array
     */
    private function getManifest()
    {
        if (null === $this->manifest) {
            $file = $this->directory . \DIRECTORY_SEPARATOR . 'manifest.json';
            $content = \file_get_contents($file);
            if (false === $content) {
                throw new \RuntimeException(
                    \sprintf('Unable to read file "%s"', $file)
                );
            }

            $json = \json_decode($content, true);
            if (\JSON_ERROR_NONE !== \json_last_error()) {
                throw new \RuntimeException(
                    \sprintf('Unable to decode json file "%s"', $file)
                );
            }

            $this->manifest = $json;
        }

        return $this->manifest;
    }

    /**
     * @param $entrypoint
     * @return array
     */
    public function getWebpackJsFiles($entrypoint)
    {
        if (!\array_key_exists($entrypoint, $this->getEntrypoints())) {
            throw new \InvalidArgumentException(
                \sprintf('Invalid entrypoint "%s"', $entrypoint)
            );
        }

        $files = $this->getEntrypoints()[$entrypoint];
        if (!\array_key_exists('js', $files)) {
            return [];
        }

        return $files['js'];
    }

    /**
     * @param $entrypoint
     * @return array
     */
    public function getWebpackCssFiles($entrypoint)
    {
        if (!\array_key_exists($entrypoint, $this->getEntrypoints())) {
            throw new \InvalidArgumentException(
                \sprintf('Invalid entrypoint "%s"', $entrypoint)
            );
        }

        $files = $this->getEntrypoints()[$entrypoint];
        if (!\array_key_exists('css', $files)) {
            return [];
        }

        return $files['css'];
    }

    /**
     * @param $entrypoint
     */
    public function renderWebpackScriptTags($entrypoint)
    {
        $files = $this->getWebpackJsFiles($entrypoint);
        foreach ($files as $file) {
            if (empty(static::$filesMap[$file])) {
                printf('<script src="%s"></script>', $file);
                static::$filesMap[$file] = true;
            }
        }
    }

    /**
     * @param $entrypoint
     */
    public function renderWebpackLinkTags($entrypoint)
    {
        $files = $this->getWebpackCssFiles($entrypoint);
        foreach ($files as $file) {
            if (empty(static::$filesMap[$file])) {
                printf('<link rel="stylesheet" href="%s">', $file);
                static::$filesMap[$file] = true;
            }
        }
    }

    /**
     * @param $resource
     * @return string
     */
    public function getAssetPath($resource)
    {
        $withoutLeadingSlash = \ltrim($resource, '/');
        $manifest = $this->getManifest();
        if (isset($manifest[$resource])) {
            return $manifest[$resource];
        }
        if (isset($manifest[$withoutLeadingSlash])) {
            return $manifest[$withoutLeadingSlash];
        }

        return $resource;
    }
}
