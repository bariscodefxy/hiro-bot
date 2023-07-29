<?php

/**
 * Copyright 2023 bariscodefx
 * 
 * This file is part of project Hiro 016 Discord Bot.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace hiro\commands;

use Discord\Voice\VoiceClient;
use React\ChildProcess\Process;
use Discord\Builders\MessageBuilder;
use hiro\parts\VoiceFile;

class Play extends Command
{
    public function configure(): void
    {
        $this->command = "play";
        $this->description = "Plays music from youtube.";
        $this->aliases = [];
        $this->category = "music";
    }

    public function playMusic($text_channel, $settings)
    {
        $voice_client = $settings->getVoiceClient();
        $author_id = $settings->getAuthorId();
        
        @unlink($author_id . ".m4a");
        @unlink($author_id . ".info.json");
        
        $command = "./yt-dlp -f bestaudio[ext=m4a] --ignore-config --ignore-errors --write-info-json --output=./{$author_id}.m4a --audio-quality=0 \"{$settings->getQueue()[0]}\"";
        $process = new Process($command);
        $process->start();

        $editmsg = $textChannel->sendMessage("Downloading audio, please wait...");

        $process->on('exit', function($code, $term) use ($voice_client, $editmsg, $settings, $author_id) {
            
            if (is_file($author_id . ".m4a")) {
                $play_file_promise = $voice_client->playFile($author_id . ".m4a");
            }
            
            $editmsg->then(function($m) use ($author_id, $play_file_promise) {
                
                if (!is_file($author_id . ".m4a")) {
                    $m->edit(MessageBuilder::new()->setContent("Couldn't download the audio."));
                    return;
                }
                
                $jsondata = json_decode(file_get_contents($author_id . ".info.json"));

                if($settings->getQueue()[$settings->currentSong])
                {
                    $this->playMusic($textChannel, $settings);
                } else {
                    $m->edit(MessageBuilder::new()->setContent("Music not found on queue."));
                }

                if (!$settings->getLoopEnabled())
                {
                    $settings->setCurrentSong( $settings->getCurrentSong() + 1 );
                }

                $m->edit(MessageBuilder::new()->setContent("Playing **{$jsondata->title}**. :musical_note: :tada:"));
                
            });
            
            $this->discord->getLoop()->addTimer(0.5, function() use ($author_id) {
                @unlink($author_id . ".m4a");
                @unlink($author_id . ".info.json");
            });
            
        });
    }

    public function handle($msg, $args): void
    {
        global $voiceSettings;
	    $channel = $msg->member->getVoiceChannel();
	    $voiceClient = $this->discord->getVoiceClient($msg->guild_id);

        if (!$channel) {
            $msg->channel->sendMessage("You must be in a voice channel.");
            return;
        }

        $url = substr($msg->content, strlen($_ENV['PREFIX'] . "play "));

        if (!$url) {
            $msg->reply("You should write a URL!");
            return;
        }

        if (!$voiceClient) {
            $msg->reply("Use the join command first.\n");
            return;
        }

        if ($voiceClient && $channel->id !== $voiceClient->getChannel()->id) {
            $msg->reply("You must be in the same channel with me.");
            return;
	    }

        $settings = @$voiceSettings[$msg->channel->guild_id];
        
	    if (!$settings)
	    {
		    $msg->reply("Voice options couldn't found.");
		    return;
	    }

        $url = str_replace('\\', '', $url);
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            $msg->sendMessage("The URL is not valid.");
            return;
        }

    	preg_match('/https?:\/\/(www\.)?youtube\.com\/watch\?v\=([A-Za-z0-9-_]+)/', $url, $matches);
    	preg_match('/https?:\/\/(www\.)?youtu\.be\/([A-Za-z0-9-_]+)/', $url, $matches2);
    	preg_match('/https?:\/\/(www\.)?youtube\.com\/shorts\/([A-Za-z0-9-_]+)/', $url, $matches3);
    	if(!@$matches[0] && !@$matches2[0] && !@$matches3[0])
    	{
    	    $msg->reply("YouTube video URL not found.\n");
    	    return;
    	}
    	$url = $matches[0] ?? $matches2[0] ?? $matches3[0];
        
        $settings->addToQueue(new VoiceFile($url, $msg->author->id));

        $this->playMusic($url, $msg->channel, $settings, $voiceClient, $msg->author->id);
    }
}
