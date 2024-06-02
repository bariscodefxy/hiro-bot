<?php

/**
 * Copyright 2021-2024 bariscodefx
 * 
 * This file part of project Hiro 016 Discord Bot.
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

use Discord\Helpers\Collection;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Command\Option;

/**
 * Slap
 */
class Slap extends Command
{
    /**
     * configure
     *
     * @return void
     */
    public function configure(): void
    {
        $this->command = "slap";
        $this->description = "You can slap everybody.";
        $this->aliases = ["tokat"];
        $this->category = "reactions";
        $this->options = [
            (new Option($this->discord))
                ->setType(Option::USER)
                ->setName('user')
                ->setDescription('User to slap')
                ->setRequired(true)
        ];
    }

    /**
     * handle
     *
     * @param [type] $msg
     * @param [type] $args
     * @return void
     */
    public function handle($msg, $args): void
    {
        global $language;
        $gifs = [
            "https://bariscodefxy.github.io/cdn/hiro/slap.gif",
            "https://bariscodefxy.github.io/cdn/hiro/slap_1.gif",
            "https://bariscodefxy.github.io/cdn/hiro/slap_2.gif",
            "https://bariscodefxy.github.io/cdn/hiro/slap_3.gif",
            "https://bariscodefxy.github.io/cdn/hiro/slap_4.gif",
            "https://bariscodefxy.github.io/cdn/hiro/slap_5.gif",
        ];
        $random = $gifs[rand(0, sizeof($gifs) - 1)];
        $self = $msg->author;
        if($args instanceof Collection && $args->get('name', 'user') !== null) {
            $user = $this->discord->users->get('id', $args->get('name', 'user')->value);
        } else if (is_array($args)) {
            $user = $msg->mentions->first();
        }
        $user ??= null;
        if (empty($user)) {
            $embed = new Embed($this->discord);
            $embed->setColor("#ff0000");
            $embed->setDescription($language->getTranslator()->trans('commands.slap.no_user'));
            $embed->setTimestamp();
            $msg->reply($embed);
            return;
        } else if ($user->id == $self->id) {
            $embed = new Embed($this->discord);
            $embed->setColor("#ff0000");
            $embed->setDescription($language->getTranslator()->trans('commands.slap.selfslap'));
            $embed->setTimestamp();
            $msg->reply($embed);
            return;
        }
        $embed = new Embed($this->discord);
        $embed->setColor("#ff0000");
        $embed->setDescription(sprintf($language->getTranslator()->trans('commands.slap.success'), $self, $user));
        $embed->setImage($random);
        $embed->setTimestamp();
        $msg->reply($embed);
    }
}
