<?php
namespace nomination\view;

use \nomination\NominationDocument;
use \nomination\CommandFactory;
use \nomination\Context;
use \nomination\NominationFactory;
use \nomination\ReferenceFactory;
use \nomination\ViewFactory;

/**
 * ReferenceForm
 *
 *   Allows References to submit their letter of recommendation.
 *
 * @author Daniel West <dwest at tux dot appstate dot edu>
 * @author Jeremy Booker
 * @package nomination
 */
class ReferenceForm extends \nomination\View {

    public function getRequestVars()
    {
        $vars = array('view'=>'ReferenceForm');

        if(isset($this->unique_id)){
            $vars['unique_id'] = $this->unique_id;
        }

        return $vars;
    }

    public function display(Context $context)
    {
        $factory = new CommandFactory;
        $submitCmd = $factory->get('SubmitRecommendation');
        $form = new \PHPWS_Form('reference_form');
        $submitCmd->initForm($form);

        // Check if unique_id is in context
        if(!isset($context['unique_id'])){
            \NQ::simple('nomination', NotificationView::NOMINATION_ERROR, 'Missing ID in link');
            $vFactory = new ViewFactory();
            $fof = $vFactory->get('FourOhFour');
            $fof->redirect();
        }

        $ref = ReferenceFactory::getByUniqueId($context['unique_id']);

        // Check that we got a reference obj back
        if(is_null($ref)){
            \NQ::simple('nomination', NotificationView::NOMINATION_ERROR, 'Invalid ID');
            $vFactory = new ViewFactory();
            $fof = $vFactory->get('FourOhFour');
            $fof->redirect();
        }

        $nom = NominationFactory::getNominationById($ref->getNominationId());

        $form->addHidden('unique_id', $context['unique_id']);

        $tpl = array();
        $tpl['RECOMMENDATION'] = NominationDocument::getFileWidget(null, 'recommendation', $form);
        $tpl['STUDENT'] = $nom->getFullName();

        $form->addSubmit('submit', 'Submit');
        $form->mergeTemplate($tpl);

        \Layout::addPageTitle('Reference Form');

        return \PHPWS_Template::process($form->getTemplate(), 'nomination', 'reference_form.tpl');
    }
}
