<?php

/**
 * This file is part of ecampusweb/background-process.
 *
 * (c) 2016 Michael Rowe
 */

namespace Cocur\BackgroundProcess;

use Exception;
use RuntimeException;

/**
 * BackgroundPhpProcess.
 *
 * Runs a php file in the background.
 *
 * @author    Michael Rowe <michael@ecampusweb.com>
 * @copyright 2016 Michael Rowe
 * @license   http://opensource.org/licenses/MIT The MIT License
 */
class BackgroundPhpProcess extends BackgroundProcess
{

    /**
     * @var false|string
     */
    private $phpCommand;

    /**
     * @param string $phpFile The php file to execute
     * @param array $args The arguments to pass to the PHP file
     * @param string $phpCommand The shell command to run PHP
     *
     * If no phpCommand is given, an attempt will be made to find the
     * systems PHP executable
     *
     * @codeCoverageIgnore
     */
    public function __construct($phpFile, $args = [], $phpCommand = '')
    {
        if($phpCommand) {
            $this->phpCommand = $phpCommand;
        } else {
            $finder = new PhpExecutableFinder();
            $this->phpCommand = $finder->find();
            if(!$this->phpCommand) {
                throw new RuntimeException('Could not find PHP executable');
            }
        }

        $command = $this->phpCommand . ' ' . $phpFile;

        if(count($args)) {
            $command .= $this->parseArgs($args);
        }

        parent::__construct($command);
    }

    private function parseArgs($args) {
        $arg_string = '';
        foreach ($args as $arg) {

            if(is_array($arg) or is_object($arg)) {
                $arg = json_encode($arg);
            }

            try {
                $arg_string .= ' ' . escapeshellarg(strval($arg));
            } catch (Exception $e) {
            }
        }

        return $arg_string;
    }
}
