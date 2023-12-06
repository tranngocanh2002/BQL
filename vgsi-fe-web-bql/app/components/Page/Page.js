import classnames from "classnames";
import PropTypes from "prop-types";
import Animate from "rc-animate";
import React, { PureComponent } from "react";
import Loader from "../Loader";
import styles from "./Page.less";

export default class Page extends PureComponent {
  render() {
    const {
      className,
      children,
      inner = false,
      loading,
      fixHeight = false,
      noPadding = false,
      noMinHeight = false,
      style,
    } = this.props;
    const loadingStyle = {
      height: "calc(100vh - 184px)",
      overflow: "hidden",
    };
    return (
      <div className={styles.pageRoot}>
        <div
          className={classnames(className, {
            [styles.contentInner]: inner,
            [styles.contentFixHeight]: fixHeight,
            [styles.contentNoPadding]: noPadding,
            [styles.contentNoMinHeight]: noMinHeight,
          })}
          style={loading ? loadingStyle : style}
        >
          {loading ? <Loader spinning /> : ""}
          {!loading && (
            <Animate transitionAppear transitionName="fade">
              {children}
            </Animate>
          )}
        </div>
      </div>
    );
  }
}

Page.propTypes = {
  className: PropTypes.string,
  children: PropTypes.node,
  loading: PropTypes.bool,
  inner: PropTypes.bool,
};
