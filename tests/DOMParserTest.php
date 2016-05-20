<?php

namespace Algolia\Test;

use Algolia\DOMParser;

class DOMParserTest extends \PHPUnit_Framework_TestCase
{
    private $content = <<<EOT
<h1>My h1 heading</h1>
    <h3>     </h3>
    <article>
        <h2>My h2 heading</h2>
                    <p>My first paragraph</p>
                    
            <h3>My h3 heading</h3>
    </article>
                <div>
                <h4>My h4 heading</h4>
                    <p>Awesome content</p>
                    <p>Other content</p>
                    <ul>
                        <li>Line 1</li>
                        <li>Line 2</li>
                    </ul>
                    <table>
                        <tr>
                            <td>Table</td>
                            <td>Content</td>
                        </tr>
                    </table>
                    <p></p>
                </div>
        <h2>Second h2</h2>
    
<h1>Another h1</h1>
EOT;

    public function testParsingLogic()
    {
        $expected = array(
            array(
                'h1'      => 'My h1 heading',
                'h2'      => 'My h2 heading',
                'h3'      => '',
                'h4'      => '',
                'h5'      => '',
                'h6'      => '',
                'content' => 'My first paragraph',
            ),
            array(
                'h1'      => 'My h1 heading',
                'h2'      => 'My h2 heading',
                'h3'      => 'My h3 heading',
                'h4'      => 'My h4 heading',
                'h5'      => '',
                'h6'      => '',
                'content' => 'Awesome content',
            ),
            array(
                'h1'      => 'My h1 heading',
                'h2'      => 'My h2 heading',
                'h3'      => 'My h3 heading',
                'h4'      => 'My h4 heading',
                'h5'      => '',
                'h6'      => '',
                'content' => 'Other content',
            ),
            array(
                'h1'      => 'My h1 heading',
                'h2'      => 'My h2 heading',
                'h3'      => 'My h3 heading',
                'h4'      => 'My h4 heading',
                'h5'      => '',
                'h6'      => '',
                'content' => 'Line 1 Line 2',
            ),
            array(
                'h1'      => 'My h1 heading',
                'h2'      => 'My h2 heading',
                'h3'      => 'My h3 heading',
                'h4'      => 'My h4 heading',
                'h5'      => '',
                'h6'      => '',
                'content' => 'Table Content',
            ),
            array(
                'h1'      => 'My h1 heading',
                'h2'      => 'Second h2',
                'h3'      => '',
                'h4'      => '',
                'h5'      => '',
                'h6'      => '',
                'content' => '',
            ),
            array(
                'h1'      => 'Another h1',
                'h2'      => '',
                'h3'      => '',
                'h4'      => '',
                'h5'      => '',
                'h6'      => '',
                'content' => '',
            ),
        );

        $parser = new DOMParser();
        $objects = $parser->parse($this->content);
        $this->assertEquals($expected, $objects);
    }

    public function testParsingFromRootSelector()
    {
        $expected = array(
            array(
                'h1'      => '',
                'h2'      => 'My h2 heading',
                'h3'      => '',
                'h4'      => '',
                'h5'      => '',
                'h6'      => '',
                'content' => 'My first paragraph',
            ),
            array(
                'h1'      => '',
                'h2'      => 'My h2 heading',
                'h3'      => 'My h3 heading',
                'h4'      => '',
                'h5'      => '',
                'h6'      => '',
                'content' => '',
            ),
        );

        $parser = new DOMParser();
        $objects = $parser->parse($this->content, 'article');
        $this->assertEquals($expected, $objects);
    }
}
