<?php

namespace BenTools\WebpackEncoreResolver\Tests;

use function BenTools\WebpackEncoreResolver\asset;
use function BenTools\WebpackEncoreResolver\encore_entry_css_files;
use function BenTools\WebpackEncoreResolver\encore_entry_js_files;
use function BenTools\WebpackEncoreResolver\encore_entry_link_tags;
use function BenTools\WebpackEncoreResolver\encore_entry_script_tags;

class AssetPathResolverTest extends \PHPUnit_Framework_TestCase
{
    const ASSETS_DIRECTORY = __DIR__.'/fixtures';

    /**
     * @test
     */
    public function it_resolves_the_asset_path()
    {
        $this->assertEquals('/assets/main.a1ef5f34.css', asset('assets/main.css', self::ASSETS_DIRECTORY));
        $this->assertEquals('/assets/main.a1ef5f34.css', asset('/assets/main.css', self::ASSETS_DIRECTORY));
        $this->assertEquals('foo.css', asset('foo.css', self::ASSETS_DIRECTORY));
    }

    /**
     * @test
     */
    public function it_returns_script_files()
    {
        $jsfiles = encore_entry_js_files('main', self::ASSETS_DIRECTORY);
        $expected = [
            '/assets/0.145f42f0.js',
            '/assets/main.7eb4664d.js'
        ];
        $this->assertEquals($expected, $jsfiles);
    }

    /**
     * @test
     */
    public function it_returns_css_files()
    {
        $cssFiles = encore_entry_css_files('main', self::ASSETS_DIRECTORY);
        $expected = [
            '/assets/main.a1ef5f34.css'
        ];
        $this->assertEquals($expected, $cssFiles);
    }

    /**
     * @test
     */
    public function it_outputs_link_tags()
    {
        $expected = <<<HTML
<link rel="stylesheet" href="/assets/main.a1ef5f34.css">
HTML;

        $this->expectOutputString($expected, encore_entry_link_tags('main', self::ASSETS_DIRECTORY));
    }

    /**
     * @test
     */
    public function it_outputs_script_tags()
    {
        $expected = <<<HTML
<script src="/assets/0.145f42f0.js"></script><script src="/assets/main.7eb4664d.js"></script>
HTML;

        $this->expectOutputString($expected, encore_entry_script_tags('main', self::ASSETS_DIRECTORY));
    }

}
