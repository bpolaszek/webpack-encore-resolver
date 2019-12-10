<?php

namespace BenTools\WebpackEncoreResolver;

/**
 * @param string $entrypoint
 * @param string|null $directory
 * @return array
 */
function encore_entry_js_files($entrypoint, $directory = null)
{
    return (new AssetPathResolver($directory))->getWebpackJsFiles($entrypoint);
}

/**
 * @param string $entrypoint
 * @param string|null $directory
 * @return array
 */
function encore_entry_css_files($entrypoint, $directory = null)
{
    return (new AssetPathResolver($directory))->getWebpackCssFiles($entrypoint);
}

/**
 * @param string $entrypoint
 * @param string|null $directory
 */
function encore_entry_script_tags($entrypoint, $directory = null)
{
    (new AssetPathResolver($directory))->renderWebpackScriptTags($entrypoint);
}

/**
 * @param string $entrypoint
 * @param string|null $directory
 */
function encore_entry_link_tags($entrypoint, $directory = null)
{
    (new AssetPathResolver($directory))->renderWebpackLinkTags($entrypoint);
}

/**
 * @param $resource
 * @param string|null $directory
 * @return array|mixed
 */
function asset($resource, $directory = null)
{
    return (new AssetPathResolver($directory))->getAssetPath($resource);
}
