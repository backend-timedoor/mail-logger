<?php

namespace Timedoor\MailLogger\Models;

use Illuminate\Database\Eloquent\Model;

class MailLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'recipients',
        'subject',
        'mailable_name',
        'body',
        'mailable',
        'is_queued',
        'is_notification',
        'notifiable',
        'is_sent',
        'tries'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'uuid' => 'string',
        'recipients' => 'array',
        'subject' => 'string',
        'mailable_name' => 'string',
        'body' => 'string',
        'mailable' => 'string',
        'is_queued' => 'boolean',
        'is_notification' => 'boolean',
        'notifiable' => 'string',
        'is_sent' => 'boolean',
        'tries' => 'integer'
    ];

    /**
     * The attributes that should be cast to date.
     *
     * @var array<string, string>
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * constructor.
     * 
     * @param array $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('mail_logger.table_name', 'mail_logs'));
    }

    /**
     * Get nootification
     * 
     * @return mixed
     */
    public function getNotificationAttribute()
    {
        return $this->attributes['mailable'];
    }

    /**
     * Set notifcation
     * 
     * @param $attribute
     */
    public function setNotificationAttribute($attribute)
    {
        $this->attributes['mailable'] = $attribute;
    }
}
