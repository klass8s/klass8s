<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling\Keda\Manifest\ScaledObject\Trigger\MySQL;

use Klass8s\Autoscaling\Keda\Manifest\ScaledObject\Trigger\AbstractTriggerBuilder;
use Klass8s\Autoscaling\Keda\Manifest\ScaledObject\Trigger\OneOf;

class MySQLTrigger extends AbstractTriggerBuilder
{
    /**
     * Format of connection string is "user:password@tcp(mysql:3306)/db_name"
     */
    private const string CONNECTION_STRING = 'connectionString';
    private const string CONNECTION_STRING_FROM_ENV = 'connectionStringFromEnv';
    private const string HOST = 'host';
    private const string HOST_FROM_ENV = 'hostFromEnv';
    private const string QUERY = 'query';
    private const string QUERY_VALUE = 'queryValue';
    private const string ACTIVATION_QUERY_VALUE = 'activationQueryValue';
    private const string PORT = 'port';
    private const string PORT_FROM_ENV = 'portFromEnv';
    private const string DB_NAME = 'dbName';
    private const string USERNAME = 'username';
    private const string PASSWORD = 'password';
    private const string PASSWORD_FROM_ENV = 'passwordFromEnv';

    public function connectionString(string $connectionString): static
    {
        return $this->set(self::CONNECTION_STRING, $connectionString);
    }

    public function connectionStringFromEnv(string $envName): static
    {
        return $this->set(self::CONNECTION_STRING_FROM_ENV, $envName);
    }

    public function query(string $query): static
    {
        return $this->set(self::QUERY, $query);
    }

    public function queryValue(int|float $value): static
    {
        return $this->set(self::QUERY_VALUE, $value);
    }

    public function activationQueryValue(int|float $value): static
    {
        return $this->set(self::ACTIVATION_QUERY_VALUE, $value);
    }

    public static function withQuery(string $query): static
    {
        $instance = new static();
        $instance->query($query);

        return $instance;
    }

    protected function requiredKeys(): array
    {
        return [
            OneOf::fromValues(self::CONNECTION_STRING, self::CONNECTION_STRING_FROM_ENV),
            self::QUERY,
            self::QUERY_VALUE,
            self::ACTIVATION_QUERY_VALUE,
        ];
    }

    protected static function type(): string
    {
        return 'mysql';
    }
}
