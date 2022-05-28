<?php namespace SiTEL\Logger;

/**
 * Sends logs to SNS , depends on the environemnt
 *
 * @author itaymoav
 * @date 2020-06-09
 */
class SNS extends \ZimLogger\Streams\aLogStream
{

    /**
     *
     * {@inheritdoc}
     * @see \ZimLogger\Streams\aLogStream::log()
     */
    protected function log(string $inp, int $severity, array $full_stack_data = []): void
    {
        $c = \app_env();
        // making sure nothing triggers this logger from here (NO TO recursion!)
        \ZimLogger\MainZim::$CurrentLogger = new \ZimLogger\Streams\Nan('nan', self::VERBOSITY_LVL_FATAL);
        $bctr = debug_backtrace();
        $bctr = print_r(array_slice($bctr, 4), true);
        $data = [
            'severity' => $severity,
            'exception_message' => $inp,
            'exception_trace' => $bctr
        ];
        if ($full_stack_data) {
            $data['request'] = $full_stack_data['request'];
            $data['php_inp_strm'] = file_get_contents('php://input');
            $data['session'] = $full_stack_data['session'];
            $data['server'] = $full_stack_data['server'];
            $data['queries'] = $full_stack_data['database'];
        }

        // DB
        try { // write to SNS
            $SnSclient = new \Aws\Sns\SnsClient([
                'version' => 'latest',
                'region' => 'us-east-1',
                'credentials' => [
                    'key' => $c['aws-by-app-access']['general-service']['key'],
                    'secret' => $c['aws-by-app-access']['general-service']['secret']
                ]
            ]);
            $json_data = \json_encode($data);
            if (! $json_data) {
                throw new \Exception('Faild JSON encoding in error logger, something wrong with $data');
            }

            $SnSclient->publish([
                'Message' => str_replace([
                    '\n',
                    ',"',
                    '\/'
                ], [
                    "\n",
                    ",\n\"",
                    '/'
                ], $json_data),
                'TopicArn' => $c['aws-by-app-access']['general-service']['sns']['error_topic']['arn']
                // 'MessageStructure' => 'Json'
            ]);
        } catch (\Throwable $e) { // making sure db crashes won't kill the email thingy
                                  // This is a log just in case of a total crash!
            $EmergencyLog = new \ZimLogger\Streams\File('BLACK_LOG_IS_DOWN_', self::VERBOSITY_LVL_FATAL, $c['log']['uri']);

            // Add DB FAILED to the message
            $data['exception_message'] = "Sending error to SNS failed.\n ORIG\n[{$data['exception_message']}]\nENDORIG\n";
            $EmergencyLog->fatal($e->getMessage(), false);
            $EmergencyLog->fatal($data, false);
        } finally {
            \ZimLogger\MainZim::$CurrentLogger = $this;
        }
    }
}