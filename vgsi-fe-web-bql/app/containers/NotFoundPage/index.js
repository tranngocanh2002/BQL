/**
 * NotFoundPage
 *
 * This is the page we show when the user visits a url that doesn't have a route
 *
 * NOTE: while this component should technically be a stateless functional
 * component (SFC), hot reloading does not currently support SFCs. If hot
 * reloading is not a necessity for you then you can refactor it and remove
 * the linting exception.
 */

import React from "react";
import { FormattedMessage } from "react-intl";

import Exception from "ant-design-pro/lib/Exception";
import messages from "./messages";
import Page from "../../components/Page/Page";

import styles from "./index.less";
/* eslint-disable react/prefer-stateless-function */
export default class NotFound extends React.PureComponent {
  render() {
    const { redirect, inner } = this.props;
    return (
      // <div style={{ display: 'flex', height: '100%', width: '100%' }} >
      <Page inner={inner} className={styles.notFoundPage}>
        <Exception
          type="404"
          desc={<FormattedMessage {...messages.desc} />}
          backText={<FormattedMessage {...messages.backText} />}
          redirect={redirect}
        />
      </Page>
      // </div>
    );
  }
}
