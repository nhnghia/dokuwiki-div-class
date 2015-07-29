<?php
/**
 * DokuWiki Plugin classes (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Huu Nghia Nguyen <huunghia.nguyen@me.com>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class syntax_plugin_classes extends DokuWiki_Syntax_Plugin {
    /**
     * @return string Syntax mode type
     */
    public function getType() {
        return 'formatting';
    }
    /**
     * @return string Paragraph type
     */
    public function getPType() {
        return  array('formatting', 'substition', 'disabled');
    }
    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort() {
        return 99;
    }

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode) {
        $this->Lexer->addEntryPattern('<block\b.*?>',$mode,'plugin_classes');
    }

    public function postConnect() { 
    	$this->Lexer->addExitPattern('</block>','plugin_classes'); 
    }
    /**
     * Handle matches of the classes syntax
     *
     * @param string $match The match of the syntax
     * @param int    $state The state of the handler
     * @param int    $pos The position in the document
     * @param Doku_Handler    $handler The handler
     * @return array Data for the renderer
     */
    public function handle($match, $state, $pos, Doku_Handler &$handler){
        $data = array();
        switch ($state) {
          case DOKU_LEXER_ENTER :
                $classes = substr($match, 7, -1);
                return array($state, $classes);

          case DOKU_LEXER_UNMATCHED :  
          		return array($state, $match);
          		
          case DOKU_LEXER_EXIT :       
          		return array($state, '');
        }
        return $data;
    }

    /**
     * Render xhtml output or metadata
     *
     * @param string         $mode      Renderer mode (supported modes: xhtml)
     * @param Doku_Renderer  $renderer  The renderer
     * @param array          $data      The data from the handler() function
     * @return bool If rendering was successful.
     */
    public function render($mode, Doku_Renderer &$renderer, $data) {
        // $data is what the function handle() return'ed.
        if($mode == 'xhtml'){
            /** @var Doku_Renderer_xhtml $renderer */
            list($state,$match) = $data;
            switch ($state) {
                case DOKU_LEXER_ENTER :      
                    $renderer->doc .= "<div $match>";
                    break;
                
                case DOKU_LEXER_UNMATCHED :  
                    $renderer->doc .= $renderer->render_text($match); 
                    break;
                    
                case DOKU_LEXER_EXIT :       
                    $renderer->doc .= "</div>"; 
                    break;
            }
            return true;
        }
        return false;
 
    }
}

// vim:ts=4:sw=4:et:
