<?php

namespace App\Models\RequestVideo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class RequestVideoData extends Model

{

    use SoftDeletes;

    protected $column_order = [null]; // Kolom yang dapat diurutkan
    protected $name_relasi  = ['custommers', 'materis'];
    protected $column_search_relation = ['name', 'title',]; // Kolom yang dapat dicari relation
    protected $order = ['created_at' => 'desc']; // Urutan default

    private function _get_datatables_query(Request $request)
    {
        $query = RequestVideo::select('video_requests.*');

        if ($request->input('search.value')) {
            $searchValue = $request->input('search.value');
            $query->where(function ($q) use ($searchValue) {
                foreach ($this->name_relasi as $i => $item) {
                    if ($i == 0) {
                        $q->orWhereHas($item, function ($query) use ($searchValue) {
                            $query->where($this->column_search_relation[0], 'like', "%{$searchValue}%");
                        });
                    } else if ($i == 1) {
                        $q->orWhereHas($item, function ($query) use ($searchValue) {
                            $query->where($this->column_search_relation[1], 'like', "%{$searchValue}%");
                        });
                    }
                }
            });
        }

        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->input('order.0.column')) {
            $orderColumn = $this->column_order[$request->input('order.0.column')];
            $orderDir = $request->input('order.0.dir');
            $query->orderBy($orderColumn, $orderDir);
        } else if ($this->order) {
            foreach ($this->order as $key => $value) {
                $query->orderBy($key, $value);
            }
        }

        return $query;
    }

    public function get_datarequestvideo(Request $request)
    {
        $query = $this->_get_datatables_query($request);
        if ($request->input('length') != -1) {
            $query->limit($request->input('length'))->offset($request->input('start'));
        }
        return $query->get();
    }


    public function count_filtered(Request $request)
    {
        $query = $this->_get_datatables_query($request);
        return $query->count();
    }

    public function count_all()
    {
        return RequestVideo::count();
    }
}
