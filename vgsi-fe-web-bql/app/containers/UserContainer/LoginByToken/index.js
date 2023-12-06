import React from "react";
import queryString from "query-string";
import { connect } from "react-redux";
import { loginTokenAction } from "../Login/actions";
import { createStructuredSelector } from "reselect";
import makeSelectLogin from "../Login/selectors";
import reducer from "../Login/reducer";
import saga from "../Login/saga";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import { injectIntl } from "react-intl";
import { compose } from "redux";
import { Redirect } from "react-router-dom";

class LoginByToken extends React.PureComponent {
  componentDidMount() {
    const search = this.props.location.search;
    let params = queryString.parse(search);
    const token = params.token;

    if (token) {
      this.props.dispatch(loginTokenAction({ token }));
    }
  }

  render() {
    const { login } = this.props;
    const { success } = login;

    if (success) {
      return <Redirect to="/main/home" />;
    }
    return <></>;
  }
}

const mapStateToProps = createStructuredSelector({
  login: makeSelectLogin(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "login", reducer });
const withSaga = injectSaga({ key: "login", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(LoginByToken));
