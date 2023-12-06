import { fromJS } from "immutable";
import requestPaymentDetailReducer from "../reducer";

describe("requestPaymentDetailReducer", () => {
  it("returns the initial state", () => {
    expect(requestPaymentDetailReducer(undefined, {})).toEqual(fromJS({}));
  });
});
