import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  FETCH_APARTMENT,
  FETCH_ALL_FEE_OF_APARTMENT,
  CREATE_ORDER,
  FETCH_MEMBER,
} from "./constants";
import {
  fetchApartmentComplete,
  fetchFeeOfApartmentComplete,
  createOrderComplete,
  fetchMemberCompleteAction,
} from "./actions";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchApartment(action) {
  try {
    let res = yield window.connection.fetchAllApartment({
      page: 1,
      pageSize: 200000,
      ...action.payload,
    });
    if (res.success) {
      yield put(fetchApartmentComplete(res.data.items));
    } else {
      yield put(fetchApartmentComplete());
    }
  } catch (error) {
    yield put(fetchApartmentComplete());
  }
}

function* _createOrder(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.createBill(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Create voucher successful.");
      } else {
        notificationBar("Lập phiếu thành công.");
      }
      yield put(createOrderComplete(res.data));
      !!callback && callback(res.data.id ? res.data.id : null);
    } else {
      yield put(createOrderComplete());
    }
  } catch (error) {
    yield put(createOrderComplete());
  }
}

function* _fetchFee(action) {
  try {
    if (!!action.payload) {
      let res = yield window.connection.fetchAllPayment({
        apartment_id: action.payload,
        pageSize: 20000,
        status: 0, //Chua thanh toan
      });
      if (res.success) {
        yield put(
          fetchFeeOfApartmentComplete({
            items: res.data.items.filter((iii) => {
              //Trường hợp đã vào bill nhưng không phải bill nháp tạo từ app
              return (
                !iii.service_bills ||
                iii.service_bills.length == 0 ||
                iii.money_collected != 0
              );
            }),
            total_count: res.data.total_count,
          })
        );
      } else {
        yield put(
          fetchFeeOfApartmentComplete({
            items: [],
            total_count: {
              total_money_collected: 0,
              total_more_money_collecte: 0,
              total_price: 0,
            },
          })
        );
      }
    } else {
      yield put(
        fetchFeeOfApartmentComplete({
          items: [],
          total_count: {
            total_money_collected: 0,
            total_more_money_collecte: 0,
            total_price: 0,
          },
        })
      );
    }
  } catch (error) {
    yield put(
      fetchFeeOfApartmentComplete({
        items: [],
        total_count: {
          total_money_collected: 0,
          total_more_money_collecte: 0,
          total_price: 0,
        },
      })
    );
  }
}
function* _fetchMembers(action) {
  try {
    let res = yield window.connection.fetchMemberOfApartment(action.payload);
    if (res.success) {
      yield put(fetchMemberCompleteAction(res.data.items));
    } else {
      yield put(fetchMemberCompleteAction());
    }
  } catch (error) {
    yield put(fetchMemberCompleteAction());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_APARTMENT, _fetchApartment),
    takeLatest(FETCH_ALL_FEE_OF_APARTMENT, _fetchFee),
    takeLatest(CREATE_ORDER, _createOrder),
    takeLatest(FETCH_MEMBER, _fetchMembers),
  ]);
}
