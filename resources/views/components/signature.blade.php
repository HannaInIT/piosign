@php
    $employee = isset($employee) ? $employee : (isset($record) ? $record : (isset($this) ? $this->record : null));
    $data = isset($this) ? ($this->data ?? []) : [];

    $firstName = $data['first_name'] ?? $employee?->first_name;
    $lastName = $data['last_name'] ?? $employee?->last_name;
    $jobTitle = $data['job_title'] ?? $employee?->job_title;
    $department = $data['department'] ?? $employee?->department;
    $phoneNumber = $data['phone_number'] ?? $employee?->phone_number;   
@endphp

<p 
    style="font-family: Arial, Helvetica, sans-serif; 
    font-size: 13px; 
    color: #222222; 
    margin: 0 0 20px 0; ">
        Met vriendelijke groet,
</p>

<table 
    cellspacing="0" 
    cellpadding="0" 
    style="font-family: Arial, Helvetica, sans-serif; 
    font-size: 13px; 
    color: #000000; 
    line-height: 1.2; 
    background: transparent;">
    <tbody>
        <tr>
            <td style="vertical-align: top; padding-right: 10px;">
                <img 
                src="{{ asset('images/signature/logo-img.png') }}"
                alt="Pionect"
                width="85"
                height="85"
                style="width: 85px; height: 85px; display: block;"
                />
            </td>

            <td style="vertical-align: top;">
                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 15px; margin: 0 0 2px 0;">
                    <strong style="font-family: Arial, Helvetica, sans-serif; font-size: 15px; font-weight: bold; color: #222222;">
                        {{$firstName}} {{$lastName}}
                    </strong>
                </p>             
                     
                @if($jobTitle)
                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; font-weight: bold; margin: 0 0 2px 0; color: #98A3A4;">
                    {{$jobTitle}}
                </p> 
                @endif         

                @if($department)
                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; margin: 0 0 2px 0; color: #15112D;">
                    {{ $department }}
                </p>  
                @endif

                @if(!$jobTitle || !$department || !$phoneNumber)
                    <table cellspacing="0" cellpadding="0" style="line-height: 0; font-size: 0;">
                        <tr>
                            <td style="height: 10px; line-height: 0; font-size: 0;"></td>
                        </tr>
                    </table>
                @endif
                           
                @if($phoneNumber)
                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; margin: 0 0 2px 0; color: #98A3A4;">
                    {{$phoneNumber}}
                </p>   
                @endif                   

                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; margin: 0 0 2px 0; color: #98A3A4;">
                    0103400308
                </p>
            </td>
        </tr>

        <tr>
            <td colspan="2" style="padding-top: 9px">
                <table cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td style="padding-right: 4px;">
                                <a href="https://www.pionect.com/">
                                    <img
                                    src="{{ asset('images/signature/icon-web.png') }}"
                                    alt="Website"
                                    width="25"
                                    height="25"
                                    style="width: 25px; height: 25px; display: block;"
                                    />
                                </a>
                            </td>

                            <td>
                                <a href="https://www.linkedin.com/company/pionect/posts/?feedView=all">
                                    <img
                                    src="{{ asset('images/signature/icon-linkedin.png') }}"
                                    alt="Linkedin"
                                    width="25"
                                    height="25"
                                    style="width: 25px; height: 25px; display: block;"                                    
                                    />
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>