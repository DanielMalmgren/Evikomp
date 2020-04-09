<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Content;
use Config;
use Illuminate\Support\Facades\Storage;

class MigrateFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:migratefiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move all lesson files from old to new file storage model';

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
     * @return mixed
     */
    public function handle()
    {
        foreach(Content::all() as $content) {
            if($content->type=='file' || $content->type=='image' || $content->type=='office' || $content->type=='audio') {
                //Storage::delete($content->filepath().$content->filename());
                $this->info("Migrating ".$content->type." content ".$content->id." (lesson ".$content->lesson->translateOrDefault(\App::getLocale())->name.")");
                if(is_null($content->content)) {
                    $this->info("   Nothing to migrate!");
                    continue;
                }
                $this->info("   Filename: ".$content->content);
                $oldpath = "public/files/".$content->filename_oldstyle();
                $newpath = "public/files/".$content->id."/".Config::get('app.fallback_locale').'/'.$content->filename();
                $this->info("   Moving file from ".$oldpath." to ".$newpath.".");
                if(Storage::missing($oldpath)) {
                    $this->info("   Source file missing!");
                    continue;
                }
                if(Storage::exists($newpath)) {
                    $this->info("   Target file already exists!");
                    continue;
                }
                Storage::move($oldpath, $newpath);
                $content->translateOrNew(Config::get('app.fallback_locale'))->text = $content->content;
                $content->content = null;
                $content->save();
            }
        }
    }
}
