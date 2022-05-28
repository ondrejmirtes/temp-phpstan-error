<?php

/**
 * Use of tabl error_monitor + emails on info | warning | error | fatal
 * Will always use full stack for warning | error | fatal
 * 
 * @author itaymoav
 */
class Logger_SambaIlluminations extends \ZimLogger\Streams\aLogStream
{

    protected function log($inp, $severity, $full_stack_data = null): void
    {
        // making sure nothing triggers this logger from here (NO TO recursion!)
        \ZimLogger\MainZim::$CurrentLogger = new \ZimLogger\Streams\Nan('nan', self::VERBOSITY_LVL_FATAL);
        if ($inp instanceof Exception) {
            $bctr = $inp->getTraceAsString();
        } else {
            $bctr = debug_backtrace(BACKTRACE_MASK);
            $bctr = 'odedejoy' . print_r(array_slice($bctr, 4), true);
        }
        $data = [
            'severity' => $severity,
            'exception_message' => ($inp instanceof Exception) ? $inp->getMessage() : $inp,
            'exception_trace' => $bctr, // ($inp instanceof Exception)?print_r($inp->getTraceAsString(),true): 'odedejoy' . print_r(debug_backtrace(BACKTRACE_MASK),true),
            'request' => $full_stack_data ? print_r($full_stack_data['request'], true) . ' XXXXXXX ' . file_get_contents('php://input') : '',
            'session' => $full_stack_data ? print_r($full_stack_data['session'], true) : '',
            'server' => $full_stack_data ? print_r($full_stack_data['server'], true) : '',
            'queries' => $full_stack_data ? print_r($full_stack_data['subscribers'] + rwdb()->getDebugData(), true) : ''
        ];

        // DB
        try { // write to DB
            $ttl = time() + (2 * 24 * 3600); // 2 days in the future
            $pk = '#error_monitoring#' . date('Y-m-d');
            $sk = date('H:i:s');
            $client = new \Aws\DynamoDb\DynamoDbClient([
                'version' => 'latest',
                'region' => 'us-east-2',
                'credentials' => [
                    'key' => 'AKIAS4CIHRHEOF4R6AJQ',
                    'secret' => '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'
                ]
            ]);
            $marsh = new \Aws\DynamoDb\Marshaler();
            $item = [
                'PK' => $pk,
                'SK' => $sk,
                'error' => $data,
                'TimeToLive' => $ttl
            ];
            $command = [
                'TableName' => 'illuminations',
                'Item' => $marsh->marshalItem($item)
            ];
            $client->putItem($command);
        } catch (Exception $e) { // making sure db crashes won't kill the email thingy
                                 // This is a log just in case of a total crash!
            $c = app_env();
            $EmergencyLog = new \ZimLogger\Streams\File('ILLUMINATIONS_LOG_IS_DOWN_', self::VERBOSITY_LVL_FATAL, $c['log']['uri']);

            // Add DB FAILED to the message
            $data['exception_message'] = 'INSERT ERROR TO DB FAILED [' . $data['exception_message'] . ']';
            $EmergencyLog->fatal($e, true);
        }
    }
}
