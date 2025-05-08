<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChapterRequest;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ChapterController extends Controller
{
    public function index()
    {

        if (!Chapter::first()) {
            return response()->json(['message' => 'Nenhum capítulo encontrado'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['data' => Chapter::all()], Response::HTTP_OK);
    }

    public function create() {}

    public function store(ChapterRequest $request)
    {
        $image = $request->file('image');
        $image_urn = $image->store('chapter/images', 'public');
        
        $url = url()->previous("/storage/$image_urn");

        $chapter = Chapter::create([
            'post_id' =>  $request->post_id,
            'title' =>  $request->title,
            'content' =>  $request->content,
            'image' =>  $url,
        ]);

        return response()->json(['data' => $chapter], Response::HTTP_CREATED);
    }

    public function show($id)
    {

        if (!Chapter::find($id)) {
            return response()->json(['message' => 'Nada encontrado'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['data' => Chapter::find($id)], Response::HTTP_OK);
    }

    public function edit(Chapter $chapter) {}

    public function update(Request $request, $id)
    {
        $chapter = Chapter::find($id);

        if (!$chapter) {
            return response()->json(['erro' => 'Nada encontrado'], Response::HTTP_NOT_FOUND);
        }
        
        if ($request->hasFile('image')) {
            $image = $request->file('image'); 
    
            if ($chapter->image) {
                $oldimage = str_replace(url('/storage') . '/', '', $chapter->image);
                Storage::disk('public')->delete($oldimage);
            }

            $image_urn = $image->store('chapter/images', 'public'); 

            $url = url()->previous("/storage/{$image_urn}");
    
            $chapter->image = $url;
        }
        
        $chapter->title = $request->title ?: $chapter->title;
        $chapter->content = $request->content ?: $chapter->content;   
        $chapter->update();

        return response()->json(
            [
                'message' => 'Atualizado com sucesso!',
                'data' => $chapter
            ],
            Response::HTTP_OK
        );
    }

    public function destroy($id)
    {
        $chapter = Chapter::find($id);

        if ($chapter === null) {
            return response()->json(
                ['erro' => 'Não foi possível realizar a exclusão desse capítulo'],
                Response::HTTP_NOT_FOUND
            );
        }
        
        Storage::disk('public')->delete(str_replace(
            url('/storage') . '/', '', $chapter->image));

        $chapter->delete();
        return response()->json(['message' => 'Capítulo excluído com sucesso!'], Response::HTTP_NO_CONTENT);
    }
}
