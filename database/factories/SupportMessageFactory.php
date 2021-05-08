<?php

namespace Nikservik\SimpleSupport\Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Nikservik\SimpleSupport\Models\SupportMessage;


class SupportMessageFactory extends Factory
{
    protected $model = SupportMessage::class;

    public function definition(): array
    {
        return [
            'message' => $this->faker->sentence(),
            'type' => $this->faker->randomElement(['supportMessage', 'userMessage']),
            'read_at' => null,
        ];
    }

    public function fromUser()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'userMessage',
            ];
        });
    }

    public function fromSupport()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'supportMessage',
            ];
        });
    }

    public function notification()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'notification',
            ];
        });
    }

    public function mayBeNotification()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => $this->faker->randomElement(['supportMessage', 'userMessage', 'notification']),
            ];
        });
    }

    public function notificationRead(SupportMessage $notification)
    {
        return $this->state(function (array $attributes) use ($notification) {
            return [
                'type' => 'notificationRead',
                'message' => $notification->id,
                'read_at' => Carbon::now(),
            ];
        });
    }

    public function replyTo(SupportMessage $message)
    {
        return $this->state(function (array $attributes) use ($message) {
            return [
                'reply_to' => $message->id,
            ];
        });
    }
}

