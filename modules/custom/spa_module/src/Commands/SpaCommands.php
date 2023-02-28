<?php

namespace Drupal\spa_module\Commands;

use Drush\Commands\DrushCommands;

class SpaCommands extends DrushCommands {
    /**
     *  @command print:message
     *  @aliases print-message
     *  @param string $message is the string
     *  @options arr to take mulktiple value
     *  @options count to return count

     *  @usage print:message
     */

    public function printMessage($message, $options = ['count' => false])
    {
        if ($options['count']) {
            $this->output()->writeln(
                'Length of this string is.' . strlen($message)
            );
        } else {
            $this->output()->writeln('The message is ' . $message);
        }
    }
}
