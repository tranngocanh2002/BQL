import { take, all, put, select, takeLatest, call } from 'redux-saga/effects';
import { delay } from 'redux-saga'
import { CHECK_TOKEN, CREATE_PASSWORD } from './constants';
import { checkTokenCompleteAction, createPasswordCompleteAction } from './actions';

function* _checkToken(actions) {
  yield delay(2000)
  yield put(checkTokenCompleteAction(true))
}
function* _createPassword(actions) {
  try {
    let res = yield window.connection.createPassword(actions.payload)
    if (res.success) {
      yield put(createPasswordCompleteAction(true))
    } else {
      yield put(createPasswordCompleteAction(false))
    }
  } catch (error) {
    console.log(`error`, error)
    yield put(createPasswordCompleteAction(false))
  }
}

// Individual exports for testing
export default function* createPasswordSaga() {
  yield all([
    takeLatest(CHECK_TOKEN, _checkToken),
    takeLatest(CREATE_PASSWORD, _createPassword),
  ])
}
