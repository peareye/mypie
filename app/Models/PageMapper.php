<?php
/**
 * Page Mapper
 */
namespace Piton\Models;

class PageMapper extends DataMapperAbstract
{
    protected $table = 'page';
    protected $modifiableColumns = ['title', 'url', 'url_locked', 'meta_description'];

    /**
     * Get Pages, and Pagelets
     *
     * Returns multidimensional array of header page fields, and child pagelet array
     * @return array
     */
    public function findPageSets()
    {
        // Get page headers, and pagelet content in one DB query
        $this->sql = 'select p.*, pl.id pagelet_id, pl.name, pl.content from page p left outer join pagelet pl on p.id = pl.page_id order by p.id, pl.name';

        $results = $this->find();

        // Rebuild array into multidimensional array
        $pages = [];
        foreach ($results as $row) {
            if (!array_key_exists($row->id, $pages)) {
                $pages[$row->id] = ['id' => $row->id, 'title' => $row->title, 'url' => $row->url];
                if ($row->pagelet_id) {
                    $pages[$row->id]['pagelets'][] = [
                        'pagelet_id' => $row->pagelet_id,
                        'name' => $row->name,
                        'content' => $row->content];
                }
            } else {
                $pages[$row->id]['pagelets'][] = [
                    'pagelet_id' => $row->pagelet_id,
                    'name' => $row->name,
                    'content' => $row->content];
            }
        }

        return $pages;
    }
}
