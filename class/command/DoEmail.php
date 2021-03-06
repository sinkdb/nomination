<?php
namespace nomination\command;

/**
 * DoEmail
 *
 *   Actually send emails, based on what type of message it is.
 *
 * @author Daniel West <dwest at tux dot appstate dot edu>
 * @author Chris Detsch
 * @package nomination
 */

 use \nomination\Command;
 use \nomination\Context;
 use \nomination\NominationEmail;
 use \nomination\UserStatus;
 use \nomination\view\NotificationView;

 use \nomination\AllNominatorsEmail;
 use \nomination\CompleteNominationEmail;
 use \nomination\IncompleteNomEmail;
 use \nomination\RefNeedUploadEmail;
 use \nomination\CompleteNomineeEmail;

class DoEmail extends Command {

    public $from;
    public $list;
    public $subject;
    public $message;

    public function getRequestVars()
    {
        $vars = array('action'=>'DoEmail');

        if(isset($this->from)){
            $vars['from'] = $this->from;
        }
        if(isset($this->list)){
            $vars['list'] = $this->list;
        }
        if(isset($this->subject)){
            $vars['subject'] = $this->subject;
        }
        if(isset($this->message)){
            $vars['message'] = $this->message;
        }

        return $vars;
    }

    public function execute(Context $context)
    {

        if(!UserStatus::isAdmin()){
            throw new PermissionException('You are not allowed to do this!');
        }

        try{
            $msgType = $context['list'];

            if($msgType === 'ALLNOM')
            {
              $mail = new AllNominatorsEmail($context['subject'], $context['message'], $msgType);
              $mail->send();
            }
            else if($msgType === 'NOMCPL')
            {
              $mail = new CompleteNominationEmail($context['subject'], $context['message'], $msgType);
              $mail->send();
            }
            else if($msgType === 'NOMINC')
            {
              $mail = new IncompleteNomEmail($context['subject'], $context['message'], $msgType);
              $mail->send();
            }
            else if($msgType === 'REFNON')
            {
              $mail = new RefNeedUploadEmail($context['subject'], $context['message'], $msgType);
              $mail->send();
            }
            else if($msgType === 'NOMINE')
            {
              $mail = new CompleteNomineeEmail($context['subject'], $context['message'], $msgType);
              $mail->send();
            }
            \NQ::simple('nomination', NotificationView::NOMINATION_SUCCESS, 'Emails sent');
            $this->redirect();

        } catch(DatabaseException $e){

            \NQ::simple('nomination', NotificationView::NOMINATION_ERROR, $e->getMessage());
        }
    }
}
