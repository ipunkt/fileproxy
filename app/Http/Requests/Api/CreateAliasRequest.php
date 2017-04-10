<?php

namespace App\Http\Requests\Api;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class CreateAliasRequest extends FormRequest
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
            'data.type' => 'required|in:aliases',
            'data.attributes.path' => 'required|min:6|max:'.MAX_STRING_LENGTH.'|unique:file_aliases,path',
            'data.attributes.hits' => 'numeric|min:0',
            'data.attributes.from' => 'sometimes|nullable|date',
            'data.attributes.until' => 'sometimes|nullable|date',
        ];
    }

    /**
     * returns path.
     *
     * @return string
     */
    public function path(): string
    {
        $data = $this->get('data', []);

        return array_get($data, 'attributes.path');
    }

    /**
     * returns hits.
     *
     * @return int|null
     */
    public function hits()
    {
        $data = $this->get('data', []);

        $hits = intval(array_get($data, 'attributes.hits', 0));

        return $hits > 0
            ? $hits
            : null;
    }

    /**
     * returns valid from.
     *
     * @return Carbon
     */
    public function validFrom(): Carbon
    {
        $data = $this->get('data', []);

        $from = array_get($data, 'attributes.from');

        if ($from === null) {
            return Carbon::now();
        }

        return Carbon::parse($from);
    }

    /**
     * returns valid until.
     *
     * @return Carbon|null
     */
    public function validUntil()
    {
        $data = $this->get('data', []);

        $until = array_get($data, 'attributes.until');

        if ($until === null) {
            return;
        }

        return Carbon::parse($until);
    }
}
