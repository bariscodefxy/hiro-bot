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

namespace hiro\parts;

use Discord\Voice\VoiceClient;

class VoiceSettings
{
    public $voiceClient;
	public array $queue = [];
	public bool $loopEnabled = false;
    public int $currentSong = 0;
    
    public function __construct(VoiceClient $vc)
    {
        $this->setVoiceClient($vc);
    }
    
    public function getVoiceClient(): ?VoiceClient
    {
        return $this->voiceClient;
    }
    
    public function getQueue(): array
    {
        return $this->queue;
    }
    
    public function getLoopEnabled(): bool
    {
        return $this->loopEnabled;
    }
    
    public function getCurrentSong(): int
    {
        return $this->currentSong;
    }
    
    public function setVoiceClient(?VoiceClient $voiceClient): VoiceClient
    {
        $this->voiceClient = $voiceClient;
    }
    
    public function addToQueue(VoiceFile $voice_file): void
    {
        $this->queue[] = $voice_file;
    }
    
    public function setLoopEnabled(bool $loop): void
    {
        $this->loopEnabled = $loop;
    }
    
    public function setCurrentSong(int $currentSong): void
    {
        $this->currentSong = $currentSong;
    }

}
