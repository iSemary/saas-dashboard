<?php

namespace Modules\Comment\Repositories;

interface CommentInterface
{
    public function all();
    public function datatables();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function restore($id);
    
    // Comment-specific methods
    public function getCommentsForObject($objectId, $objectModel);
    public function getTopLevelComments($objectId, $objectModel);
    public function getReplies($parentId);
    public function addReaction($commentId, $userId, $reactionType);
    public function removeReaction($commentId, $userId);
    public function markAsSeen($commentId);
    public function getUnseenCount($userId);
}
