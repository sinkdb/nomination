<?php
namespace nomination\othermenu;

class MainMenu extends OtherMenu
{
    // Overriding constructor from othermenu's MenuItem class
    public function __construct(){}

    public function addMenu($text, $tag)
    {
        $this[$tag] = new OtherMenu($text, $tag);
    }

    public function insertNewColumn()
    {
        $this['new_column'] = new NewColumn();
    }

    public function show()
    {
        $tpl = array();

        foreach($this->container as $item){
            // Show each item
            $tpl['menus'][] = array('CONTENT' => $item->show());
        }
        \Layout::addStyle('nomination', 'othermenu/css/style.css');
        return \PHPWS_Template::process($tpl, 'nomination', 'othermenu/main_menu.tpl');
    }
}

// This class restarts the div in the template
// See templates/main_menu.tpl
class NewColumn
{
    public function show(){
        return "</div><div class='main-menu'>";
    }
}
