<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use GuzzleHttp\Psr7\Request;

class test_olive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:olive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $client = new Client();
        $headers = [
            'Content-Type' => 'application/xml',
            'Authorization' => 'Basic b2xpdmU6UG1FQDdvTHYh'
        ];
        $body = '<Attandance>
    <Employee>
        <EmpID>51950</EmpID>
        <ProjectID>20422</ProjectID>
        <DutyDate>5/9/2022</DutyDate>
        <Morning>P</Morning>
        <Afternoon>P</Afternoon>
        <TotalHours>10</TotalHours>
        <IncentiveHours>2</IncentiveHours>
        <OffDayOTHours>0</OffDayOTHours>
        <HolidayOTHours>0</HolidayOTHours>
        <OffDayOrHoliday>HOLIDAY</OffDayOrHoliday>
        <Remarks>api test</Remarks>
        <BatchNo>151</BatchNo>
        <ExportUser>viewtech</ExportUser>
    </Employee>
</Attandance>';
        $request = new Request('POST', 'protectauh.fortiddns.com:8082/Api/Attendance/UploadEmployeeAttendance', $headers, $body);
        $res = $client->sendAsync($request)->wait();
        echo $res->getBody();

        return 0;
    }
}
