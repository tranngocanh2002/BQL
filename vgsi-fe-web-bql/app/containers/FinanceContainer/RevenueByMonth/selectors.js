import { createSelector } from "reselect";
import { initialState } from "./reducer";
import _ from "lodash";
const selectRevenueByMonth = (state) =>
  state.get("RevenueByMonth", initialState);

const makeSelectRevenueByMonth = () =>
  createSelector(selectRevenueByMonth, (substate) => {
    let total = 0;
    let total_paid = 0;
    let total_unpaid = 0;
    const loading = _.get(substate.toJS(), ["loading"], false);
    const res = _.get(substate.toJS(), ["data"], []);
    const data = res && res.data;
    res &&
      res.data &&
      res.data.map((item) => {
        total = item.total + total;
        total_paid = item.total_paid + total_paid;
        total_unpaid = item.total_unpaid + total_unpaid;
      });
    return { loading, total, total_paid, total_unpaid, data };
  });

export default makeSelectRevenueByMonth;
export { selectRevenueByMonth };
