<?php defined("SYSPATH") or die("No direct access allowed.");
/**
 * @author Jon Blanton <jon@2600hz.com>
 * @license MPL
 * @package VMAlert-1.0
 */
class FreeSwitch_VMAlert_Driver {
    public static function set($base) {
        $basepath = self::getScriptsPath();
        $vmalert = $base['plugins']['vmalert'];
        $vmid = "voicemail_" . $base['account_id'];
        $filename = $basepath . "/" . $vmid . ".lua";
        
        if(isset($vmalert['enabled'])) {
            // This is probably not 100% safe....
            $fp = fopen($filename, "w");
            fwrite($fp, self::returnLuaTemplate("default", $vmid, $base['mailbox'], $vmalert['number'], $vmalert['context']));
            fclose($fp);
        }
        else {
            if(file_exists($filename)) {
                unlink($filename);
            }
        }
    }

    private static function getScriptsPath() {
        $path = Kohana::config('freeswitch.cfg_root');

        // This is not a good idea...
        $path = preg_replace("/(?<=\/)[^\/]+$/", "scripts", $path);

        return $path;
    }

    private static function returnLuaTemplate($vmdomain, $vmbox, $vmuser, $number, $context) {
        $template = "script_name = \"VM Alert\"\n" .
                    "timeout = 10\n" .
                    "api = freeswitch.API()\n" .
                    "freeswitch.consoleLog(\"info\", script_name .. \": Checking if there are any new voicemails.\\n\")\n" .
                    "vmcount = tonumber(api:executeString(\"vm_boxcount " . $vmuser . "@" . $vmbox . "|new\"))\n" .
                    "if (vmcount > 0) then\n" .
                    "   session = freeswitch.Session(\"[voicemail_authorized=false,origination_caller_id_number=1,origination_caller_id_name=Voicemail]loopback/" . $number . "/context_" . $context . "\")\n" .
                    "   freeswitch.consoleLog(\"info\", script_name .. \": New voicemails found, calling the 'call number'...\\n\")\n" .
                    "   i = 0\n" .
                    "   while (session:answered() == false) do\n" .
                    "       session:execute(\"sleep\", \"1000\")\n" .
                    "       if (i == timeout) then\n" .
                    "           break\n" .
                    "       else\n" .
                    "           i = i + 1\n" .
                    "       end\n" .
                    "   end\n" .
                    "   if session:answered() then\n" .
                    "       freeswitch.consoleLog(\"info\", script_name .. \": The callie picked up. Running IVR... \\n\")" .
                    "       session:execute(\"playback\", \"ivr/ivr-hello.wav\")\n" .
                    "       session:execute(\"playback\", \"voicemail/vm-you_have.wav\")\n" .
                    "       session:say(vmcount .. \"\", \"en\", \"number\", \"pronounced\")\n" .
                    "       session:execute(\"playback\", \"voicemail/vm-new.wav\")\n" .
                    "       if (vmcount == 1) then\n" .
                    "               session:execute(\"playback\", \"voicemail/vm-message.wav\")\n" .
                    "       else\n" .
                    "               session:execute(\"playback\", \"voicemail/vm-messages.wav\")\n" .
                    "       end\n" .
                    "       session:execute(\"sleep\", \"250\")\n" .
                    "       session:execute(\"playback\", \"voicemail/vm-listen_new.wav\")\n" .
                    "       session:execute(\"playback\", \"voicemail/vm-press.wav\")\n" .
                    "       session:execute(\"playback\", \"digits/1.wav\")\n" .
                    "       session:execute(\"sleep\", \"250\")\n" .
                    "       digits = session:playAndGetDigits(1, 1, 1, 10000, \"\", \"ivr/ivr-you_may_exit_by_hanging_up.wav\", \"\", \"\")\n" .
                    "       if (digits == \"1\") then\n" .
                    "            session:execute(\"sleep\", \"2000\")\n" .
                    "            freeswitch.consoleLog(\"info\", script_name .. \": Call through, transfering to vm!\\n\")\n" .
                    "            session:execute(\"voicemail\", \"check " . $vmdomain . " " . $vmbox . " " . $vmuser . "\")\n" .
                    "            freeswitch.consoleLog(\"info\", script_name .. \": VM done, hanging up.\\n\")\n" .
                    "       else\n" .
                    "            freeswitch.consoleLog(\"info\", script_name .. \": Bad DTMF or the B-Leg hungup, exiting.\\n\")\n" .
                    "       end\n" .
                    "   else\n" .
                    "       freeswitch.consoleLog(\"info\", script_name .. \": Never answered, hanging up.\\n\")\n" .
                    "   end\n" .
                    "else\n" .
                    "   freeswitch.consoleLog(\"info\", script_name .. \": No new voicemails, exiting.\\n\")\n" .
                    "end\n";
        return $template;
    }

    public static function delete($base) {
        $basepath = self::getScriptsPath();
        $vmalert = $base['plugins']['vmalert'];
        $vmid = "voicemail_" . $base['account_id'];
        $filename = $basepath . "/" . $vmid . ".lua";
        
        if(file_exists($filename)) {
                unlink($filename);
        }
    }
}
