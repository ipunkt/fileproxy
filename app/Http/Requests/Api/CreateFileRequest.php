<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class CreateFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'data.type' => 'required|in:files',
            'data.attributes.type' => 'required|in:attachment,uri',
            'data.attributes.source' => 'required',
            'data.attributes.filename' => 'sometimes|required_if:attributes.type,attachment',
        ];
    }

    /**
     * is it an attachment or not.
     *
     * @return bool
     */
    public function isAttachment(): bool
    {
        $data = $this->get('data', []);

        return array_get($data, 'attributes.type', 'uri') === 'attachment';
    }

    /**
     * returns the source: base64 encoded file content or url.
     *
     * @return string
     */
    public function source(): string
    {
        $data = $this->get('data', []);

        return $this->isAttachment()
            ? base64_decode(array_get($data, 'attributes.source'))
            : array_get($data, 'attributes.source');
    }

    /**
     * returns filename.
     *
     * @return string
     */
    public function filename(): string
    {
        $data = $this->get('data', []);

        return $this->isAttachment()
            ? array_get($data, 'attributes.filename')
            : basename(array_get($data, 'attributes.source'));
    }

    /**
     * Get the proper failed validation response for the request.
     *
     * @param  array  $errors
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response(array $errors)
    {
        $e = collect($errors)->mapWithKeys(function ($error, $key) {
            return [$key => $error[0]];
        });

        $data = [
            'status' => 422,
            'code' => 422,
            'title' => 'Validation error',
            'source' => [
                'pointer' => $e->all(),
            ],
        ];

        return new JsonResponse($data, 422);
    }
}
