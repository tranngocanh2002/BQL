import _ from "lodash";
import React from "react";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { selectAuthGroup } from "../../redux/selectors";

class WithRole extends React.PureComponent {
  render() {
    const { roles, children, auth_group } = this.props;
    if (!roles || roles.length == 0 || !auth_group) return null;
    const { data_role } = auth_group;
    if (!data_role) return null;
    if (_.xor(data_role, roles).length == data_role.length + roles.length) {
      return null;
    }
    return children;
  }
}

const mapStateToProps = createStructuredSelector({
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

export default connect(mapStateToProps, mapDispatchToProps)(WithRole);
