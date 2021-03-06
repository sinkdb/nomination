<?php
namespace nomination;

/**
 * EmailLogFactory
 *
 * Factory class for loading and saving EmailLog objects to and from the database.
 *
 * @author Chris Detsch
 * @package nomination
 */
class EmailLogFactory {

    /**
     * Saves the values of an EmailLog object to the database table nomination_email_log,
     * if there was already a log with the given EmailLog's id then it will overwrite it
     * otherwise it will create a new id.
     *
     * @param $email EmailLog
     */
    public static function save(EmailLog $email)
    {
        $db = new \PHPWS_DB('nomination_email_log');

        $db->addValue('nominee_id', $email->getNomineeId());
        $db->addValue('message', $email->getMessage());
        $db->addValue('message_type', $email->getMessageType());
        $db->addValue('subject', $email->getSubject());
        $db->addValue('receiver_id', $email->getReceiverId());
        $db->addValue('receiver_type', $email->getReceiverType());
        $db->addValue('sent_on', $email->getSentOn());

        $id = $email->getId();
        if(!isset($id) || is_null($id)) {
            $result = $db->insert();

        }else{
            $db->addWhere('id', $id);
            $result = $db->update();
        }

        if(\PHPWS_Error::logIfError($result)) {
            throw new \Exception('DatabaseException: Failed to save EmailLog. ' . $result->toString());
        }
    }

    /**
     * Returns the EmailLog object with the given uniqueId, or null if
     * no matching email is found.
     *
     * @param string $id - The email's ID.
     * @return DBEmailLog object, or null if no matching id found
     */
    public static function getById($id){


        $db = new \PHPWS_DB('nomination_email_log');

        $db->addWhere('id', $id);

        $result = $db->select('row');

        if(\PHPWS_Error::logIfError($result)){
            \PHPWS_Core::initModClass('nomination', 'exception/DatabaseException.php');
            throw new exception\DatabaseException($result->toString());
        }

        if(count($result) == 0){
            return null;
        }

        $email = new DBEmailLog();
        $email->setId($result['id']);
        $email->setNomineeId($result['nominee_id']);
        $email->setMessage($result['message']);
        $email->setMessageType($result['message_type']);
        $email->setSubject($result['subject']);
        $email->setReceiverId($result['receiver_id']);
        $email->setReceiverType($result['receiver_type']);
        $email->setSentOn($result['sent_on']);

        return $email;
    }

    /**
     * Delete nomination with the given id
     *
     * @param $id Nomination id
     */
    public static function deleteEmailLogByNomId($id)
    {
      if(!isset($id))
      {
          throw new \InvalidArgumentException('Missing id.');
      }

      $db = new \PHPWS_DB('nomination_email_log');

      $db->addWhere('nominee_id', $id);
      $db->delete();
    }

}
