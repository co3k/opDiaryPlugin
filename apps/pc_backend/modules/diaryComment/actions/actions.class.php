<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * diaryComment actions.
 *
 * @package    OpenPNE
 * @subpackage diaryComment
 * @author     Rimpei Ogawa <ogawa@tejimaya.com>
 */
class diaryCommentActions extends sfActions
{
  public function preExecute()
  {
    if (is_callable(array($this->getRoute(), 'getObject')))
    {
      $object = $this->getRoute()->getObject();
      if ($object instanceof Diary)
      {
        $this->diary = $object;
      }
      elseif ($object instanceof DiaryComment)
      {
        $this->diaryComment = $object;
        $this->diary = $this->diaryComment->getDiary();
      }
    }
  }

  public function executeList(sfWebRequest $request)
  {
    $this->pager = Doctrine::getTable('DiaryComment')->getDiaryCommentPager($request->getParameter('page'), 20);
    $this->pager->init();
  }

  public function executeSearch(sfWebRequest $request)
  {
    if ($request->hasParameter('diary_id'))
    {
      $this->diaryId = $request->getParameter('diary_id');
      $this->pager = Doctrine::getTable('DiaryComment')->getDiaryCommentPagerForDiary($this->diaryId, $request->getParameter('page'), 20);
    }
    elseif ($request->hasParameter('keyword'))
    {
      $this->keyword = $request->getParameter('keyword');
      $keywords = opDiaryPluginToolkit::parseKeyword($this->keyword);
      $this->pager = Doctrine::getTable('DiaryComment')->getDiaryCommentSearchPager($keywords, $request->getParameter('page'), 20);
    }
    else
    {
      $this->forward('diaryComment', 'list');
    }

    $this->pager->init();
    $this->setTemplate('list');
  }

  public function executeDeleteConfirm(sfWebRequest $request)
  {
    $this->form = new sfForm();
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->diaryComment->delete();

    $this->getUser()->setFlash('notice', 'The comment was deleted successfully.');

    $this->redirect('diaryComment/list');
  }
}
