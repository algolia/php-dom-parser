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
                    <p>
                        Other content
                        <pre>
                            Some code that should not be present.
                        </pre>
                        
                    </p>
                    <pre>
                        Some code that should not be present.
                    </pre>
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
        <script>alert('hello');</script>
<h1>Another h1</h1>
EOT;

    public function testParsingLogic()
    {
        $expected = array(
            array(
                'title1'  => 'My h1 heading',
                'title2'  => 'My h2 heading',
                'title3'  => '',
                'title4'  => '',
                'title5'  => '',
                'title6'  => '',
                'content' => 'My first paragraph',
            ),
            array(
                'title1'  => 'My h1 heading',
                'title2'  => 'My h2 heading',
                'title3'  => 'My h3 heading',
                'title4'  => 'My h4 heading',
                'title5'  => '',
                'title6'  => '',
                'content' => 'Awesome content',
            ),
            array(
                'title1'  => 'My h1 heading',
                'title2'  => 'My h2 heading',
                'title3'  => 'My h3 heading',
                'title4'  => 'My h4 heading',
                'title5'  => '',
                'title6'  => '',
                'content' => 'Other content',
            ),
            array(
                'title1'  => 'My h1 heading',
                'title2'  => 'My h2 heading',
                'title3'  => 'My h3 heading',
                'title4'  => 'My h4 heading',
                'title5'  => '',
                'title6'  => '',
                'content' => 'Line 1 Line 2',
            ),
            array(
                'title1'  => 'My h1 heading',
                'title2'  => 'My h2 heading',
                'title3'  => 'My h3 heading',
                'title4'  => 'My h4 heading',
                'title5'  => '',
                'title6'  => '',
                'content' => 'Table Content',
            ),
            array(
                'title1'  => 'My h1 heading',
                'title2'  => 'Second h2',
                'title3'  => '',
                'title4'  => '',
                'title5'  => '',
                'title6'  => '',
                'content' => '',
            ),
            array(
                'title1'  => 'Another h1',
                'title2'  => '',
                'title3'  => '',
                'title4'  => '',
                'title5'  => '',
                'title6'  => '',
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
                'title1'  => '',
                'title2'  => 'My h2 heading',
                'title3'  => '',
                'title4'  => '',
                'title5'  => '',
                'title6'  => '',
                'content' => 'My first paragraph',
            ),
            array(
                'title1'  => '',
                'title2'  => 'My h2 heading',
                'title3'  => 'My h3 heading',
                'title4'  => '',
                'title5'  => '',
                'title6'  => '',
                'content' => '',
            ),
        );

        $parser = new DOMParser();
        $objects = $parser->parse($this->content, 'article');
        $this->assertEquals($expected, $objects);
    }
}
