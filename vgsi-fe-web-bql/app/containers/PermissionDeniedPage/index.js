/**
 *
 * PermissionDeniedPage
 *
 */

import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { FormattedMessage } from 'react-intl';
import { compose } from 'redux';

import Exception from 'ant-design-pro/lib/Exception';
import messages from './messages';
import Page from '../../components/Page/Page';
/* eslint-disable react/prefer-stateless-function */
export class PermissionDeniedPage extends React.PureComponent {
  render() {
    return (
      <Page inner>
        <Exception
          type="403"
          desc={<FormattedMessage {...messages.desc} />}
          backText={<FormattedMessage {...messages.backText} />}
          redirect="/main/home"
        />
      </Page>
    );
  }
}

// PermissionDeniedPage.propTypes = {
//   dispatch: PropTypes.func.isRequired,
// };

// function mapDispatchToProps(dispatch) {
//   return {
//     dispatch,
//   };
// }

// const withConnect = connect(
//   null,
//   mapDispatchToProps,
// );

export default PermissionDeniedPage; // compose(withConnect)(PermissionDeniedPage);
