<?php

namespace Lexik\Bundle\JWTAuthenticationBundle\Signature;

/**
 * Object representation of a JSON Web Signature loaded from an
 * existing JSON Web Token.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class LoadedJWS
{
    const VERIFIED = 'verified';
    const EXPIRED  = 'expired';

    /**
     * @var array
     */
    private $payload;

    /**
     * @var string
     */
    private $state;

    /**
     * @param array $payload
     * @param bool  $isVerified
     */
    public function __construct(array $payload, $isVerified)
    {
        $this->payload = $payload;

        if (true === $isVerified) {
            $this->state = self::VERIFIED;
        }

        $this->checkExpiration();
    }

    /**
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return bool
     */
    public function isVerified()
    {
        return self::VERIFIED === $this->state;
    }

    /**
     * @return bool
     */
    public function isExpired()
    {
        $this->checkExpiration();

        return self::EXPIRED === $this->state;
    }

    /**
     * Ensures that the signature is not expired.
     */
    private function checkExpiration()
    {
        if (!isset($this->payload['exp']) || !is_numeric($this->payload['exp'])) {
            return $this->state = null;
        }

        if (0 <= (new \DateTime())->format('U') - $this->payload['exp']) {
            $this->state = self::EXPIRED;
        }
    }
}