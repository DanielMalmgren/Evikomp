<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Lesson;
use App\Content;

class CheckLinksInLessons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:checklinks {--nomail}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check links in all lessons and report if any broken links are found.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function testurl($url) {
        $file_headers = @get_headers($url);
        if(!is_array($file_headers)) {
            $this->info('Got no headers at all for '.$url);
            return false;
        }
        foreach($file_headers as $file_header) {
            if(strpos($file_header, 'HTTP/1.1 404') !== false) {
                $this->info('Got header '.$file_header.' for '.$url);
                return false;
            }
        }
        return true;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        logger("Checking all links for errors");
        $mailtext = '';
        $this->info("The following links have problems:");
        $contents = Content::where('type', 'html')->get();

        stream_context_set_default( [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        foreach($contents as $content) {
            $translations = $content->translations()->get();
            foreach($translations as $translation) {
                preg_match_all("(href=\"(.*?)(\"))", $translation->text, $links);
                if(isset($links[1])) {
                    foreach($links[1] as $link) {
                        if(!$this->testurl($link)) {
                            $this->info("URL: ".$link." in lesson ".$content->lesson->translateOrDefault($translation->locale)->name." (".$translation->locale.")");
                            $mailtext .= $link." i modulen <a href=\"".env('APP_URL')."/lessons/".$content->lesson->id."\">".$content->lesson->translateOrDefault($translation->locale)->name." (".$translation->locale.")</a><br>";
                        }
                    }
                }
            }
        }

        if(strlen($mailtext) > 0 && !$this->option('nomail')) {
            $to = [];
            $to[] = ['email' => env('FEEDBACK_RECIPIENT_ADDRESS'), 'name' => env('FEEDBACK_RECIPIENT_NAME')];

            \Mail::to($to)->send(new \App\Mail\Linkcheck($mailtext));
        }
    }
}
