<?php

namespace Microservices\Queue;

use Illuminate\Queue\Failed\DatabaseUuidFailedJobProvider;

class MongoUuidFailedJobProvider extends DatabaseUuidFailedJobProvider
{

    public function find($id)
    {
        $record = $this->getTable()->where('uuid', $id)->first();
        if (!$record) {
            $record = $this->getTable()->where('payload', 'like', '%"uuid":"' . $id . '"%')->first();
        }
        if (!$record && $this->isMongoObjectId($id)) {
            $record = $this->getTable()->where('_id', $id)->first();
        }
        return $this->normalizeRecord($record);
    }

    public function forget($id)
    {
        if (parent::forget($id)) {
            return true;
        }

        if ($this->getTable()->where('payload', 'like', '%"uuid":"' . $id . '"%')->delete() > 0) {
            return true;
        }

        if ($this->isMongoObjectId($id)) {
            return $this->getTable()->where('_id', $id)->delete() > 0;
        }

        return false;
    }

    protected function isMongoObjectId(string $id): bool
    {
        return (bool) preg_match('/^[a-f0-9]{24}$/i', $id);
    }

    public static function resolveUuid(mixed $record): ?string
    {
        if (empty($record)) {
            return null;
        }

        $data = is_array($record) ? $record : (array) $record;

        if (!empty($data['uuid'])) return (string) $data['uuid'];

        $payload = $data['payload'] ?? null;

        if (!is_string($payload) || $payload === '') return null;

        $decoded = json_decode($payload, true);

        return !empty($decoded['uuid']) ? (string) $decoded['uuid'] : null;
    }

    protected function normalizeRecord(mixed $record): ?object
    {
        if (empty($record)) {
            return null;
        }

        $data = is_array($record) ? $record : (array) $record;
        $uuid = self::resolveUuid($data);

        if ($uuid === null) {
            return null;
        }

        return (object) [
            'id' => $uuid,
            'connection' => (string) ($data['connection'] ?? ''),
            'uuid' => (string) ($data['uuid'] ?? ''),
            'queue' => (string) ($data['queue'] ?? ''),
            'payload' => is_string($data['payload'] ?? null) ? $data['payload'] : '',
            'exception' => (string) ($data['exception'] ?? ''),
            'failed_at' =>  $data['failed_at'] ?? null,
        ];
    }

    protected function normalizeFailedAt(mixed $failedAt): string
    {
        if ($failedAt instanceof \DateTimeInterface) {
            return $failedAt->format('Y-m-d H:i:s');
        }

        if ($failedAt instanceof \MongoDB\BSON\UTCDateTime) {
            return $failedAt->toDateTime()->format('Y-m-d H:i:s');
        }

        if (is_numeric($failedAt)) {
            $timestamp = (int) $failedAt;

            // Mongo thường lưu milliseconds
            if ($timestamp > 9999999999) {
                $timestamp = (int) floor($timestamp / 1000);
            }

            return date('Y-m-d H:i:s', $timestamp);
        }

        if (is_array($failedAt)) {
            if (!empty($failedAt['date'])) {
                return (string) $failedAt['date'];
            }

            return '';
        }

        return is_string($failedAt) ? $failedAt : '';
    }
}
