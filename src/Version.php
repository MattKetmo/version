<?php
/**
 * This file is part of the Version package.
 *
 * Copyright (c) Nikola Posa <posa.nikola@gmail.com>
 *
 * For full copyright and license information, please refer to the LICENSE file,
 * located at the package root folder.
 */

namespace Version;

use Version\Metadata\PreRelease;
use Version\Metadata\Build;
use Version\Exception\InvalidArgumentException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
final class Version
{
    /**
     * @var int
     */
    private $major;

    /**
     * @var int
     */
    private $minor;

    /**
     * @var int
     */
    private $patch;

    /**
     * @var PreRelease
     */
    private $preRelease = null;

    /**
     * @var Build
     */
    private $build = null;

    /**
     * @param int $major
     * @param int $minor
     * @param int $patch
     * @param PreRelease $preRelease OPTIONAL
     * @param Build $build OPTIONAL
     */
    public function __construct($major, $minor, $patch, PreRelease $preRelease = null, Build $build = null)
    {
        if (!is_int($major) || $major < 0) {
            throw new InvalidArgumentException('Major version must be non-negative integer');
        }

        if (!is_int($minor) || $minor < 0) {
            throw new InvalidArgumentException('Minor version must be non-negative integer');
        }

        if (!is_int($patch) || $patch < 0) {
            throw new InvalidArgumentException('Patch version must be non-negative integer');
        }

        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
        $this->preRelease = $preRelease;
        $this->build = $build;
    }

    /**
     * @param string $versionString
     * @return self
     * @throws Exception\InvalidArgumentException
     */
    public static function fromString($versionString)
    {
        $parts = [];

        if (!preg_match(
            '#^(?P<core>(?:[0-9]|[1-9][0-9]+)\.(?:[0-9]|[1-9][0-9]+)\.(?:[0-9]|[1-9][0-9]+))(?:\-(?P<preRelease>[0-9A-Za-z\-\.]+))?(?:\+(?P<build>[0-9A-Za-z\-\.]+))?$#',
            $versionString,
            $parts
        )) {
            throw new Exception\InvalidVersionStringException("Version string is not valid and cannot be parsed");
        }

        list($major, $minor, $patch) = explode('.', $parts['core']);
        $major = (int) $major;
        $minor = (int) $minor;
        $patch = (int) $patch;

        $preRelease = (!empty($parts['preRelease'])) ? PreRelease::fromString($parts['preRelease']) : null;

        $build = (!empty($parts['build'])) ? Build::fromString($parts['build']) : null;

        return new self($major, $minor, $patch, $preRelease, $build);
    }

    /**
     * @return int
     */
    public function getMajor()
    {
        return $this->major;
    }

    /**
     * @return int
     */
    public function getMinor()
    {
        return $this->minor;
    }

    /**
     * @return int
     */
    public function getPatch()
    {
        return $this->patch;
    }

    /**
     * @return PreRelease[]
     */
    public function getPreRelease()
    {
        return $this->preRelease;
    }

    /**
     * @return Build[]
     */
    public function getBuild()
    {
        return $this->build;
    }

    /**
     * @return string
     */
    public function getVersionString()
    {
        return
            $this->major
            . '.' . $this->minor
            . '.' . $this->patch
            . (!empty($this->preRelease) ? '-' . implode('.', $this->preRelease) : '')
            . (!empty($this->build) ? '+' . implode('.', $this->build) : '')
        ;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getVersionString();
    }
}