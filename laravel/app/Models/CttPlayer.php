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
                if ($this->isLaravelEncryptedPayload($this->password)) {
                    throw new \RuntimeException(
                        'The stored AFTT password is encrypted but cannot be decrypted with the current APP_KEY. Restore the APP_KEY used when it was saved, or re-enter the AFTT password.',
                        0,
                        $innerException
                    );
                }

                return $this->password;
            }
        }
    }

    private function isLaravelEncryptedPayload(string $value): bool
    {
        $decoded = base64_decode($value, true);
        if ($decoded === false) {
            return false;
        }

        $payload = json_decode($decoded, true);

        return is_array($payload)
            && isset($payload['iv'], $payload['value'], $payload['mac']);
    }
}