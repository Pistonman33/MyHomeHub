<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;

class CttPlayer extends Model
{
    protected $table = 'ctt_players';

    // Primary key is 'license' and it's not auto-incrementing
    protected $primaryKey = 'license';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'license',
        'firstname',
        'lastname',
        'status',
        'club',
        'password',
    ];

    protected $casts = [
        'license' => 'integer',
    ];

    protected $hidden = [
        'password',
    ];

    public function getDecryptedPasswordAttribute(): ?string
    {
        if (! $this->password) {
            return null;
        }

        $encrypter = app('encrypter');

        try {
            $value = $encrypter->decryptString($this->password);

            if (is_string($value) && preg_match('/^(?:a:|b:|i:|d:|s:|O:|N;|C:)/', trim($value))) {
                $unserialized = @unserialize($value);

                if ($unserialized !== false || $value === 'b:0;' || $value === 'N;') {
                    return is_scalar($unserialized) ? (string) $unserialized : $value;
                }
            }

            return $value;
        } catch (DecryptException | \Throwable $e) {
            try {
                return $encrypter->decrypt($this->password);
            } catch (DecryptException | \Throwable $innerException) {
                return $this->password;
            }
        }
    }
}