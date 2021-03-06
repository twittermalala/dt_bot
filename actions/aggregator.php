<?php

defined('TWITTERBOT') or die('Restricted.');

require_once(BOTROOT . DIRECTORY_SEPARATOR . 'storage.php');
require_once(PHIREHOSE . 'OauthPhirehose.php');

/**
 * This class handles reading from the Streaming API and saving any
 * posts it receives. It makes use of the Phirehose PHP implementation
 * for reading the streaming API and ensuring good behavior.
 *
 * Phirehose: http://code.google.com/p/phirehose/
 * Streaming API: http://dev.twitter.com/pages/streaming_api
 *
 * @author Shannon Quinn
 */
class DataAggregator extends OauthPhirehose {

  /** a database handle for storing the data */
  private $db;

  /**
   * (non-PHPdoc)
   * @see util/Phirehose::enqueueStatus()
   */
  public function enqueueStatus($status) {
    if (!isset($this->db)) { $this->db = Storage::getDatabase(); }

    // save the status
    $data = json_decode($status, true);
    if (is_array($data) && isset($data['user']['screen_name'])) {
      $this->db->savePost($data['text'], $data['user']['screen_name']);
    }
  }

  /**
   * (non-PHPdoc)
   * @see util/Phirehose::log()
   */
  protected function log($message) {
    if (!isset($this->db)) { $this->db = Storage::getDatabase(); }
    $this->db->log('Phirehose', $message);
  }
}

?>
