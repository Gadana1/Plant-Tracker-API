<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlantResource;
use App\Http\Resources\PlantResourceList;
use App\Repository\PlantRepositoryInterface;
use Illuminate\Http\Request;

class PlantController extends Controller
{
    private $plantRepository;

    public function __construct(PlantRepositoryInterface $plantRepository)
    {
        $this->plantRepository = $plantRepository;
    }

    /**
     * Get list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $limit = intval($request->get('limit'));
        $query = $request->get('query');

        return response()->json(new PlantResourceList($this->plantRepository->paginateQuery($limit, $query)), 200);
    }

    /**
     * Create record
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $data = $this->validate($request->all(), [
            'name' => 'required|string',
            'species' => 'required|string',
            'instructions' => 'required|string',
            'image' => 'required|mimes:png,jpg,jpeg,webp,gif,svg',
        ]);

        if (($upload = $request->file('image')) && $imagePath = $upload->storeAs('uploads', uniqid(time()) . '_' . $upload->getClientOriginalName(), 'public')) {
            $result = $this->plantRepository->create([
                'name' => $data['name'],
                'species' => $data['species'],
                'instructions' => $data['instructions'],
                'image' => $imagePath,
            ]);
            if ($result) {
                return response()->json(new PlantResource($result), 200);
            }
            return response()->json(__('Failed to add plant'), 500);
        }
        return response()->json(__('Failed to upload image'), 500);
    }
}
