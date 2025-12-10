<?php

namespace Tuezy\Infrastructure\Persistence\Contact;

use Tuezy\Domain\Contact\Contact;
use Tuezy\Domain\Contact\ContactRepository as DomainContactRepository;

class PDODbContactRepository implements DomainContactRepository
{
    private $d;

    public function __construct($d)
    {
        $this->d = $d;
    }

    public function getById(int $id): ?Contact
    {
        $row = $this->d->rawQueryOne("SELECT * FROM #_contact WHERE id = ? LIMIT 0,1", [$id]);
        if (!$row) {
            return null;
        }
        return Contact::fromArray($row);
    }

    public function getAll(array $filters = [], int $start = 0, int $limit = 20): array
    {
        $where = "1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $where .= " AND find_in_set(?, status)";
            $params[] = $filters['status'];
        }

        if (!empty($filters['keyword'])) {
            $where .= " AND (fullname LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $keyword = "%{$filters['keyword']}%";
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
        }

        if (!empty($filters['date_from'])) {
            $where .= " AND date_created >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $where .= " AND date_created <= ?";
            $params[] = $filters['date_to'];
        }

        $rows = $this->d->rawQuery(
            "SELECT * FROM #_contact WHERE {$where} ORDER BY date_created DESC LIMIT {$start}, {$limit}",
            $params
        );

        return array_map(fn($r) => Contact::fromArray($r), $rows ?: []);
    }

    public function count(array $filters = []): int
    {
        $where = "1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $where .= " AND find_in_set(?, status)";
            $params[] = $filters['status'];
        }

        if (!empty($filters['keyword'])) {
            $where .= " AND (fullname LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $keyword = "%{$filters['keyword']}%";
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
        }

        $result = $this->d->rawQueryOne("SELECT COUNT(*) as total FROM #_contact WHERE {$where}", $params);
        return (int)($result['total'] ?? 0);
    }

    public function create(Contact $contact): bool
    {
        $data = $contact->toArray();
        unset($data['id']);
        if (!isset($data['date_created'])) {
            $data['date_created'] = time();
        }
        if (!isset($data['numb'])) {
            $data['numb'] = 1;
        }
        return $this->d->insert('contact', $data);
    }

    public function update(int $id, array $data): bool
    {
        $this->d->where('id', $id);
        return $this->d->update('contact', $data);
    }

    public function delete(int $id): bool
    {
        $this->d->where('id', $id);
        return $this->d->delete('contact');
    }

    public function markAsRead(int $id): bool
    {
        return $this->update($id, ['status' => 'hienthi,daxem']);
    }
}

