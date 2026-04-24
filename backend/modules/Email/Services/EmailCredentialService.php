<?php

namespace Modules\Email\Services;

use Modules\Email\DTOs\CreateEmailCredentialData;
use Modules\Email\Entities\EmailCredential;
use Modules\Email\Repositories\EmailCredentialInterface;

class EmailCredentialService
{
    protected $repository;
    public $model;

    public function __construct(EmailCredentialInterface $repository, EmailCredential $emailCredential)
    {
        $this->model = $emailCredential;
        $this->repository = $repository;
    }

    public function getAll(array $conditions = [])
    {
        return $this->repository->all($conditions);
    }

    public function getDataTables()
    {
        return $this->repository->datatables();
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function create(CreateEmailCredentialData $data)
    {
        return $this->repository->create([
            'name' => $data->name,
            'description' => $data->description,
            'from_address' => $data->from_address,
            'from_name' => $data->from_name,
            'mailer' => $data->mailer,
            'host' => $data->host,
            'port' => $data->port,
            'username' => $data->username,
            'password' => $data->password,
            'encryption' => $data->encryption,
            'status' => $data->status ?? 'active',
        ]);
    }

    public function update($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function findOrFail(int $id): EmailCredential
    {
        return EmailCredential::findOrFail($id);
    }

    public function testConnection(int $id, string $testEmail): void
    {
        $credential = EmailCredential::findOrFail($id);

        \Illuminate\Support\Facades\Config::set('mail.mailers.smtp.host', $credential->host);
        \Illuminate\Support\Facades\Config::set('mail.mailers.smtp.port', $credential->port);
        \Illuminate\Support\Facades\Config::set('mail.mailers.smtp.username', $credential->username);
        \Illuminate\Support\Facades\Config::set('mail.mailers.smtp.password', $credential->password);
        \Illuminate\Support\Facades\Config::set('mail.mailers.smtp.encryption', $credential->encryption);
        \Illuminate\Support\Facades\Config::set('mail.from.address', $credential->from_address);
        \Illuminate\Support\Facades\Config::set('mail.from.name', $credential->from_name);

        \Illuminate\Support\Facades\Mail::raw('This is a test email from your SMTP configuration.', function ($message) use ($testEmail, $credential) {
            $message->to($testEmail)
                    ->subject('SMTP Configuration Test')
                    ->from($credential->from_address, $credential->from_name);
        });
    }

    public function restore($id)
    {
        return $this->repository->restore($id);
    }
}
