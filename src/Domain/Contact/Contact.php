<?php

namespace Tuezy\Domain\Contact;

class Contact
{
    public ?int $id;
    public string $fullname;
    public string $email;
    public string $phone;
    public string $address;
    public string $subject;
    public string $content;
    public int $date_created;
    public int $numb;
    public ?string $status;
    public ?string $file_attach;

    public function __construct(
        ?int $id,
        string $fullname,
        string $email,
        string $phone,
        string $address,
        string $subject,
        string $content,
        int $date_created,
        int $numb = 1,
        ?string $status = null,
        ?string $file_attach = null
    ) {
        $this->id = $id;
        $this->fullname = $fullname;
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;
        $this->subject = $subject;
        $this->content = $content;
        $this->date_created = $date_created;
        $this->numb = $numb;
        $this->status = $status;
        $this->file_attach = $file_attach;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['fullname'] ?? '',
            $data['email'] ?? '',
            $data['phone'] ?? '',
            $data['address'] ?? '',
            $data['subject'] ?? '',
            $data['content'] ?? '',
            $data['date_created'] ?? time(),
            $data['numb'] ?? 1,
            $data['status'] ?? null,
            $data['file_attach'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'fullname' => $this->fullname,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'subject' => $this->subject,
            'content' => $this->content,
            'date_created' => $this->date_created,
            'numb' => $this->numb,
            'status' => $this->status,
            'file_attach' => $this->file_attach,
        ];
    }
}

