<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Message;

use Psr\Http\Message\StreamInterface;

/**
 * Abstract message.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractMessage implements MessageInterface
{
    /** @var string */
    protected $protocolVersion;

    /** @var array */
    protected $headers = array();

    /** @var array */
    protected $headerNames = array();

    /** @var \Psr\Http\Message\StreamInterface|null */
    protected $body;

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function setProtocolVersion($protocolVersion)
    {
        $this->protocolVersion = $protocolVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeaders()
    {
        return !empty($this->headers);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        $headers = array();

        foreach ($this->headers as $name => $value) {
            $headers[$this->headerNames[$name]] = $value;
        }

        return $headers;
    }

    /**
     * {@inheritdoc}
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array();
        $this->headerNames = array();

        foreach ($headers as $header => $value) {
            $this->setHeader($header, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addHeaders(array $headers)
    {
        foreach ($headers as $header => $value) {
            $this->addHeader($header, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeHeaders($headers)
    {
        foreach ($headers as $header) {
            $this->removeHeader($header);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader($header)
    {
        return isset($this->headers[$this->fixHeader($header)]);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($header)
    {
        return implode(', ', $this->getHeaderAsArray($header));
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderAsArray($header)
    {
        return $this->hasHeader($header) ? $this->headers[$this->fixHeader($header)] : array();
    }

    /**
     * {@inheritdoc}
     */
    public function setHeader($header, $value)
    {
        $this->removeHeader($header);
        $this->addHeader($header, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function addHeader($header, $value)
    {
        $this->headerNames[$this->fixHeader($header)] = trim($header);
        $this->headers[$this->fixHeader($header)] = array_merge(
            $this->getHeaderAsArray($header),
            array_map('trim', is_array($value) ? $value : explode(',', $value))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function removeHeader($header)
    {
        unset($this->headerNames[$this->fixHeader($header)]);
        unset($this->headers[$this->fixHeader($header)]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasBody()
    {
        return $this->body !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function setBody(StreamInterface $body = null)
    {
        $this->body = $body;
    }

    /**
     * Fixes the header.
     *
     * @param string $header The header.
     *
     * @return string The fixed header.
     */
    protected function fixHeader($header)
    {
        return strtolower(trim($header));
    }
}
