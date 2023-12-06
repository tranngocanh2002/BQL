import { fromJS } from "immutable";
import verifyOTPReducer from "../reducer";

describe("verifyOTPReducer", () => {
  it("returns the initial state", () => {
    expect(verifyOTPReducer(undefined, {})).toEqual(fromJS({}));
  });
});
