<?php

namespace App\Http\Controllers;

use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;

class KBBIController extends Controller
{
    use ApiTrait;

    public function index(Request $request)
    {
        $request->validate([
            'text' => 'required',
        ]);

        $result = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Kamu adalah sebuah Kamus KBBI, saya akan memberika input berupa kata dan kamu akan memberikan definisinya, HANYA BERIKAN DEFINISINYA SAJA, jangan berikan apapun lagi. Apabila memang yang diberikan bukan sebuah kata valid, maka berikan pesan error seperti ini: "Tak dapat menemukan definisi kata {kata}". Jika kata-kata punya lebih dari 1 arti, pisahkan saja dengan tanda titik koma (;)',
                ],
                [
                    'role' => 'user',
                    'content' => $request->text,
                ],
            ],
        ]);

        return $this->sendResponse('Success', data: [
            'text' => $result->choices[0]->message->content,
        ], code: 200);
    }
}
