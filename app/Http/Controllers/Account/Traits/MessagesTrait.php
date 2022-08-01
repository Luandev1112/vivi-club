<?php
/**
 * JobClass - Job Board Web Application
 * Copyright (c) BedigitCom. All Rights Reserved
 *
 * Website: https://bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

namespace App\Http\Controllers\Account\Traits;

use App\Models\Thread;

trait MessagesTrait
{
	/**
	 * @param $threadId
	 * @return array|void
	 */
	public function markAsRead($threadId)
	{
		// Get Entries ID
		$ids = $this->getSelectedIds($threadId);
		
		$count = is_array($ids) ? count($ids) : 0;
		if ($count <= 0) {
			return;
		}
		
		foreach ($ids as $id) {
			// Get the Thread
			$thread = $this->findThread($id);
			
			if (!empty($thread)) {
				$thread->markAsRead(auth()->id());
			}
		}
		
		if ($count > 1) {
			$msg = t('x entities has been marked as action successfully', [
				'entities' => t('messages'),
				'count'    => $count,
				'action'   => mb_strtolower(t('read')),
			]);
		} else {
			$msg = t('1 entity has been marked as action successfully', [
				'entity' => t('message'),
				'action' => mb_strtolower(t('read')),
			]);
		}
		
		return ['success' => true, 'msg' => $msg];
	}
	
	/**
	 * @param $threadId
	 * @return array|void
	 */
	public function markAsUnread($threadId)
	{
		// Get Entries ID
		$ids = $this->getSelectedIds($threadId);
		
		$count = is_array($ids) ? count($ids) : 0;
		if ($count <= 0) {
			return;
		}
		
		foreach ($ids as $id) {
			// Get the Thread
			$thread = $this->findThread($id);
			
			if (!empty($thread)) {
				$thread->markAsUnread(auth()->id());
			}
		}
		
		if ($count > 1) {
			$msg = t('x entities has been marked as action successfully', [
				'entities' => t('messages'),
				'count'    => $count,
				'action'   => mb_strtolower(t('unread')),
			]);
		} else {
			$msg = t('1 entity has been marked as action successfully', [
				'entity' => t('message'),
				'action' => mb_strtolower(t('unread')),
			]);
		}
		
		return ['success' => true, 'msg' => $msg];
	}
	
	/**
	 * @param $threadId
	 * @return array|void
	 */
	public function markAsImportant($threadId)
	{
		// Get Entries ID
		$ids = $this->getSelectedIds($threadId);
		
		$count = is_array($ids) ? count($ids) : 0;
		if ($count <= 0) {
			return;
		}
		
		foreach ($ids as $id) {
			// Get the Thread
			$thread = $this->findThread($id);
			
			if (!empty($thread)) {
				$thread->markAsImportant(auth()->id());
			}
		}
		
		if ($count > 1) {
			$msg = t('x entities has been marked as action successfully', [
				'entities' => t('messages'),
				'count'    => $count,
				'action'   => mb_strtolower(t('important')),
			]);
		} else {
			$msg = t('1 entity has been marked as action successfully', [
				'entity' => t('message'),
				'action' => mb_strtolower(t('important')),
			]);
		}
		
		return ['success' => true, 'msg' => $msg];
	}
	
	/**
	 * @param $threadId
	 * @return array|void
	 */
	public function markAsNotImportant($threadId)
	{
		// Get Entries ID
		$ids = $this->getSelectedIds($threadId);
		
		$count = is_array($ids) ? count($ids) : 0;
		if ($count <= 0) {
			return;
		}
		
		foreach ($ids as $id) {
			// Get the Thread
			$thread = $this->findThread($id);
			
			if (!empty($thread)) {
				$thread->markAsNotImportant(auth()->id());
			}
		}
		
		if ($count > 1) {
			$msg = t('x entities has been marked as action successfully', [
				'entities' => t('messages'),
				'count'    => $count,
				'action'   => mb_strtolower(t('not important')),
			]);
		} else {
			$msg = t('1 entity has been marked as action successfully', [
				'entity' => t('message'),
				'action' => mb_strtolower(t('not important')),
			]);
		}
		
		return ['success' => true, 'msg' => $msg];
	}
	
	/**
	 * Delete Thread
	 *
	 * @param null $threadId
	 * @return array|void
	 */
	public function delete($threadId = null)
	{
		// Get Entries ID
		$ids = $this->getSelectedIds($threadId);
		
		$count = is_array($ids) ? count($ids) : 0;
		if ($count <= 0) {
			return;
		}
		
		// Delete
		$nb = 0;
		foreach ($ids as $id) {
			// Get the Thread
			$thread = $this->findThread($id);
			
			if (!empty($thread)) {
				if (empty($thread->deleted_by)) {
					// Delete the Entry for current user
					Thread::withoutTimestamps()
						->where('id', $thread->id)
						->update([
							'deleted_by' => auth()->id()
						]);
					
					$nb = 1;
				} else {
					// If the 2nd user delete the Entry,
					// Delete the Entry (definitely)
					if ($thread->deleted_by != auth()->id()) {
						$nb = $thread->forceDelete();
					}
				}
			}
		}
		
		// Confirmation
		if ($nb == 0) {
			$success = false;
			$msg = t('no_deletion_is_done');
		} else {
			$success = true;
			$count = count($ids);
			if ($count > 1) {
				$msg = t('x entities has been deleted successfully', [
					'entities' => t('messages'),
					'count'    => $count,
				]);
			} else {
				$msg = t('1 entity has been deleted successfully', [
					'entity' => t('message'),
				]);
			}
		}
		
		return ['success' => $success, 'msg' => $msg];
	}
	
	/**
	 * Mark all Threads as read
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function markAllAsRead()
	{
		// Get all Threads with New Messages
		$threadsWithNewMessage = Thread::whereHas('post', function ($query) {
			$query->currentCountry();
		})->forUserWithNewMessages(auth()->id());
		
		// Count all Thread
		$countThreadsWithNewMessage = $threadsWithNewMessage->count();
		
		if ($countThreadsWithNewMessage > 0) {
			foreach ($threadsWithNewMessage->cursor() as $thread) {
				$thread->markAsRead(auth()->id());
			}
			$msg = t('x entities has been marked as action successfully', [
				'entities' => t('messages'),
				'count'    => $countThreadsWithNewMessage,
				'action'   => mb_strtolower(t('read')),
			]);
			$result = ['success' => true, 'msg' => $msg];
		} else {
			$result = [
				'success' => false,
				'msg'     => t('This action could not be done'),
			];
		}
		
		return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
	}
	
	/* PRIVATE */
	
	/**
	 * @param $id
	 * @return mixed
	 */
	private function findThread($id)
	{
		$thread = Thread::where((new Thread)->getTable() . '.id', $id)
			->forUser(auth()->id())
			->first();
		
		return $thread;
	}
	
	/**
	 * @param $entryId
	 * @return array|mixed
	 */
	private function getSelectedIds($entryId)
	{
		$ids = [];
		if (request()->filled('entries')) {
			$ids = request()->input('entries');
		} else {
			if (!is_numeric($entryId) && $entryId <= 0) {
				$ids = [];
			} else {
				$ids[] = $entryId;
			}
		}
		
		return $ids;
	}
}
